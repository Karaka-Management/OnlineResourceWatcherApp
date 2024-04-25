<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\OnlineResourceWatcher
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\OnlineResourceWatcher\Controller;

use Modules\Admin\Models\NullAccount;
use Modules\Messages\Models\EmailMapper;
use Modules\OnlineResourceWatcher\Models\Inform;
use Modules\OnlineResourceWatcher\Models\InformBlacklistMapper;
use Modules\OnlineResourceWatcher\Models\InformMapper;
use Modules\OnlineResourceWatcher\Models\Report;
use Modules\OnlineResourceWatcher\Models\ReportMapper;
use Modules\OnlineResourceWatcher\Models\ReportStatus;
use Modules\OnlineResourceWatcher\Models\Resource;
use Modules\OnlineResourceWatcher\Models\ResourceMapper;
use Modules\OnlineResourceWatcher\Models\ResourceStatus;
use Modules\OnlineResourceWatcher\Models\SettingsEnum as OrwSettingsEnum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\System\File\FileUtils;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\MimeType;
use phpOMS\System\OperatingSystem;
use phpOMS\System\SystemType;
use phpOMS\System\SystemUtils;
use phpOMS\Utils\StringUtils;

/**
 * OnlineResourceWatcher controller class.
 *
 * @package Modules\OnlineResourceWatcher
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Text renderable file formats
     *
     * @var string[]
     * @since 1.0.0
     */
    public const TEXT_RENDERABLE = ['md', 'txt', 'doc', 'docx', 'pdf', 'xls', 'xlsx'];

    /**
     * Image renderable file formats
     *
     * @var string[]
     * @since 1.0.0
     */
    public const IMG_RENDERABLE = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', 'svg'];

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceRender(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var \Modules\OnlineResourceWatcher\Models\Resource $resource */
        $resource = ResourceMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        if ($request->hasData('path')) {
            // @todo check if user has permission?
            $this->app->moduleManager->get('Media', 'Api')
                ->apiMediaExport($request, $response, ['guard' => __DIR__ . '/../Files']);

            return;
        }

        $path     = '';
        $basePath = __DIR__ . '/../Files/' . $resource->path . '/' . $resource->lastVersionPath;

        if (\is_file($basePath . '/index.htm')) {
            $path = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $resource->lastVersionPath . '/index.jpg';

            if (!\is_file(__DIR__ . '/../../../' . $path)) {
                $path = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $resource->lastVersionPath . '/index.htm';
            }
        } else {
            $files = \scandir($basePath);
            $path  = '';

            if ($files !== false) {
                foreach ($files as $file) {
                    if ($file === '.' || $files === '..') {
                        continue;
                    }

                    $path = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $resource->lastVersionPath . '/' . $file;
                }
            }
        }

        if ($path === '') {
            $response->header->status = RequestStatusCode::R_404;
            $response->set('', '');

            return;
        }

        $internalRequest                  = new HttpRequest();
        $internalRequest->header->account = $request->header->account;
        $internalRequest->setData('path', $path);
        $this->app->moduleManager->get('Media', 'Api')->apiMediaExport($internalRequest, $response, ['guard' => __DIR__ . '/../Files']);
    }

    /**
     * Validate resource create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateResourceCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['uri'] = (!$request->hasData('uri')
                || (\filter_var($request->getDataString('uri'), \FILTER_VALIDATE_URL) === false && \stripos($request->getDataString('uri') ?? '', 'www.') !== 0))
            )
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateResourceCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $resource = $this->createResourceFromRequest($request);
        $this->createModel($request->header->account, $resource, ResourceMapper::class, 'resource', $request->getOrigin());

        $this->checkResources($request, [$resource]);

        $this->createStandardCreateResponse($request, $response, $resource);
    }

    /**
     * Method to create Resource from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Resource
     *
     * @since 1.0.0
     */
    private function createResourceFromRequest(RequestAbstract $request) : Resource
    {
        $resource        = new Resource();
        $resource->owner = new NullAccount($request->header->account);
        $resource->title = $request->getDataString('title') ?? '';
        $resource->uri   = $request->getDataString('uri') ?? '';
        $resource->owner = new NullAccount($request->header->account);
        $resource->path  = $request->getDataString('path') ?? '';

        // @todo check if user is part of organization below AND has free resources to add!!!
        $resource->organization = new NullAccount($request->getDataInt('organization') ?? 1);

        return $resource;
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCheckResources(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Resource[] $resources */
        $resources = ResourceMapper::getAll()
            ->with('owner')
            ->with('owner/l11n')
            ->with('inform')
            ->where('status', ResourceStatus::ACTIVE)
            ->where('checkedAt', (new \DateTime('now'))->sub(new \DateInterval('PT12H')), '<')
            ->executeGetArray();

        $changes = $this->checkResources($request, $resources);
        $this->informEmail($changes);

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Resources', 'Resources were checked.', null);
    }

    /**
     * Inform users about changed resources
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function informUsers(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $dateTime = new \DateTime('now');
        $dateTime = $dateTime->modify('-1 hour');

        /** @var \Modules\OnlineResourceWatcher\Models\Report[] $reports */
        $reports = ReportMapper::getAll()
            ->where('status', ReportStatus::CHANGE)
            ->where('createdAt', $dateTime, '>=')
            ->executeGetArray();

        $ids = \array_map(
            function (Report $report) : int {
                return $report->resource;
            },
            $reports
        );

        /** @var Resource[] $resources */
        $resources = ResourceMapper::getAll()
            ->with('owner')
            ->with('owner/l11n')
            ->where('id', $ids, 'IN')
            ->executeGetArray();

        $this->informEmail($resources);
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Inform', 'Users were informed', null);
    }

    /**
     * Inform users about changed resources via email
     *
     * @param Resource[] $resources Changed resources
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function informEmail(array $resources) : void
    {
        $handler = $this->app->moduleManager->get('Admin', 'Api')->setUpServerMailHandler();

        /** @var \Model\Setting $templateSettings */
        $templateSettings = $this->app->appSettings->get(
            names: OrwSettingsEnum::ORW_CHANGE_MAIL_TEMPLATE,
            module: 'OnlineResourceWatcher'
        );

        /** @var \Modules\Messages\Models\Email $baseEmail */
        $baseEmail = EmailMapper::get()
            ->with('l11n')
            ->where('id', (int) $templateSettings->content)
            ->execute();

        /** @var \Modules\OnlineResourceWatcher\Models\InformBlacklist[] */
        $blacklist = InformBlacklistMapper::getAll()
            ->executeGetArray();

        foreach ($resources as $resource) {
            $owner              = new Inform();
            $owner->email       = $resource->owner->getEmail();
            $resource->inform[] = $owner;

            foreach ($resource->inform as $inform) {
                if (empty($inform->email)) {
                    continue;
                }

                foreach ($blacklist as $block) {
                    if (\stripos($inform->email, $block->email) !== false) {
                        continue 2;
                    }
                }

                $mail = clone $baseEmail;

                $status = false;
                if ($mail->id !== 0) {
                    $status = $this->app->moduleManager->get('Admin', 'Api')->setupEmailDefaults($mail, $this->app->l11nServer->language);
                }

                $mail->template = \array_merge(
                    $mail->template,
                    [
                        '{resource.id}'  => (string) $resource->id,
                        '{email}'        => $inform->email,
                        '{resource.url}' => $resource->uri,
                        '{owner_email}'  => $resource->owner->getEmail(),
                    ]
                );

                $mail->msgHTML($mail->body);

                $mail->addTo($inform->email);

                if ($status) {
                    $status = $handler->send($mail);
                }

                if (!$status) {
                    \phpOMS\Log\FileLogger::getInstance()->error(
                        \phpOMS\Log\FileLogger::MSG_FULL, [
                            'message' => 'Couldn\'t send resource mail: ' . $mail->id . ' - ' . $resource->id,
                            'line'    => __LINE__,
                            'file'    => self::class,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Checks resources for changes
     *
     * @param RequestAbstract $request   Request
     * @param array           $resources Resources to check
     * @param mixed           $data      Generic data
     *
     * @return Resource[]
     *
     * @since 1.0.0
     */
    public function checkResources(RequestAbstract $request, array $resources, mixed $data = null) : array
    {
        $changed = [];
        $toCheck = [];

        $basePath = __DIR__ . '/../Files';
        Directory::delete($basePath . '/temp');

        if (!\is_dir($basePath . '/temp')) {
            \mkdir($basePath . '/temp');
        }

        // Load resources
        foreach ($resources as $resource) {
            $path      = $basePath . '/';
            $timestamp = \time();

            $path .= 'temp/' . $resource->id . '/' . $timestamp;
            $toCheck[] = [
                'resource'  => $resource,
                'timestamp' => $timestamp,
                'path'      => $path,
                'handled'   => false,
                'loop'      => 0,
            ];

            try {
                SystemUtils::runProc(
                    OperatingSystem::getSystem() === SystemType::WIN ? 'wget.exe' : 'wget',
                    '--retry-connrefused --waitretry=1 --read-timeout=10 --timeout=10 --dns-timeout=10 -t 2 --quota=25m --adjust-extension --span-hosts --convert-links --no-directories --restrict-file-names=windows --no-parent ‐‐execute robots=off --limit-rate=5m -U mozilla --accept css,png,jpg,jpeg,webp,gif,svg,htm,html,txt,md,pdf,xls,xlsx,doc,docx --directory-prefix=' . \escapeshellarg($path) . ' ‐‐output-document=index.htm ' . \escapeshellarg($resource->uri),
                    true
                );
            } catch (\Throwable $t) {
                $this->app->logger->error(
                    \phpOMS\Log\FileLogger::MSG_FULL, [
                        'message' => $t->getMessage(),
                        'line'    => __LINE__,
                        'file'    => self::class,
                    ]
                );
            }
        }

        // Check downloaded resources
        $totalCount = \count($toCheck);
        $maxLoops   = 60 * 10; // At most wait 600 times per individual resource
        $startTime  = \time();
        $minTime    = $startTime + ((int) \max(15 * $totalCount, 60 * 15)); // At least run 15 seconds per element or 15 minutes in total
        $maxTime    = $startTime + ((int) \min(60 * $totalCount, 60 * 60 * 3)); // At most run 60 seconds per element or 3 hours in total

        while (!empty($toCheck)) {
            $time = \time();

            foreach ($toCheck as $index => $check) {
                ++$toCheck[$index]['loop'];

                /** @var Resource $resource */
                $resource = $check['resource'];

                // Too many tries
                //      1. Too many iterations and at least the min execution time is reached
                //      2. Execution takes longer than max time
                if (($check['loop'] > $maxLoops && $time > $minTime) || $time > $maxTime) {
                    $report              = new Report();
                    $report->resource    = $resource->id;
                    $report->versionPath = (string) $check['timestamp'];
                    $report->status      = ReportStatus::DOWNLOAD_ERROR;

                    $this->createModel($request->header->account, $report, ReportMapper::class, 'report', $request->getOrigin());
                    $old                 = clone $resource;
                    $resource->checkedAt = $report->createdAt;
                    $this->updateModel($request->header->account, $old, $resource, ResourceMapper::class, 'resource', $request->getOrigin());

                    unset($toCheck[$index]);

                    $this->app->logger->warning(
                        \phpOMS\Log\FileLogger::MSG_FULL, [
                            'message' => 'Resource "' . $resource->id . ': ' . $resource->uri . '" took too long to download (' . ($time - $startTime) . ' s).',
                            'line'    => __LINE__,
                            'file'    => self::class,
                        ]
                    );

                    continue;
                }

                $path = $check['path'];
                if (!\is_dir($path)) {
                    // Either the download takes too long or the download failed!
                    // Let's go to the next element and re-check later on.
                    // However, an element will only get checked a finite amount of times (limited by checks AND/OR total time)
                    continue;
                }

                // new resource
                $filesNew = \scandir($path);
                if ($filesNew === false) {
                    $filesNew = [];
                }

                $oldPath   = '';
                $newPath   = '';
                $extension = '';

                $fileName = '';
                $hasHtml  = false;
                if (\in_array('index.htm', $filesNew)
                    || ($hasHtml = \in_array('index.html', $filesNew))
                ) {
                    if ($hasHtml) {
                        \rename($path . '/index.html', $path . '/index.htm');
                    }

                    $extension                  = 'htm';
                    $fileName                   = 'index.htm';
                    $toCheck[$index]['handled'] = true;
                } else {
                    foreach ($filesNew as $file) {
                        if ($file === '..' || $file === '.') {
                            continue;
                        }

                        $extension         = ($pos = \strrpos($file, '.')) === false ? '' : \substr($file, $pos + 1);
                        $mimeContentType   = \mime_content_type($path . '/' . $file);
                        $possibleExtension = MimeType::mimeToExtension($mimeContentType === false ? '' : $mimeContentType);

                        $newFileName = FileUtils::makeSafeFileName($file);
                        if ($possibleExtension !== null && $possibleExtension !== $extension) {
                            $extension = $possibleExtension;
                            $newFileName .= '.' . $extension;
                        }

                        if ($file !== $newFileName) {
                            \rename($path . '/' . $file, $path . '/' . $newFileName);
                            $file = $newFileName;
                        }

                        if (StringUtils::endsWith($file, '.' . $extension)) {
                            $fileName                   = $file;
                            $toCheck[$index]['handled'] = true;

                            break;
                        }
                    }
                }

                // Is new resource
                if (!\is_dir($basePath . '/' . $resource->id)) {
                    $report              = new Report();
                    $report->resource    = $resource->id;
                    $report->versionPath = (string) $check['timestamp'];

                    $hash = '';
                    if (empty($fileName)) {
                        $report->status = ReportStatus::DOWNLOAD_ERROR;
                    } else {
                        $report->status = ReportStatus::ADDED;
                        $hash           = \md5_file($path . '/' . $fileName);
                    }

                    $this->createModel($request->header->account, $report, ReportMapper::class, 'report', $request->getOrigin());
                    $old                       = clone $resource;
                    $resource->path            = $resource->id === 0 ? '' : (string) $resource->id;
                    $resource->lastVersionPath = (string) $check['timestamp'];
                    $resource->lastVersionDate = $report->createdAt;
                    $resource->hash            = $hash == false ? '' : $hash;
                    $resource->checkedAt       = $report->createdAt;
                    $this->updateModel($request->header->account, $old, $resource, ResourceMapper::class, 'resource', $request->getOrigin());

                    Directory::copy($path, $basePath . '/' . $resource->id . '/' . $check['timestamp']);
                    unset($toCheck[$index]);

                    if ($extension === 'htm') {
                        try {
                            // Tool: software used is wkhtmltopdf
                            SystemUtils::runProc(
                                OperatingSystem::getSystem() === SystemType::WIN ? 'wkhtmltoimage.exe' : 'wkhtmltoimage',
                                \escapeshellarg($resource->uri) . ' ' . \escapeshellarg($basePath . '/' . $resource->id . '/' . $check['timestamp'] . '/index.jpg'),
                                true
                            );
                        } catch (\Throwable $t) {
                            $this->app->logger->error(
                                \phpOMS\Log\FileLogger::MSG_FULL, [
                                    'message' => $t->getMessage(),
                                    'line'    => __LINE__,
                                    'file'    => self::class,
                                ]
                            );
                        }
                    }

                    continue;
                }

                // existing resource
                $resourcePaths = \scandir($basePath . '/' . $resource->id);
                if ($resourcePaths === false) {
                    $resourcePaths = [];
                }

                \natsort($resourcePaths);

                $lastVersionTimestamp = \end($resourcePaths);
                if ($lastVersionTimestamp === '.' || $lastVersionTimestamp === '..') {
                    $lastVersionTimestamp = \reset($resourcePaths);
                }

                $lastVersionPath = $basePath . '/' . $resource->id . '/' . $lastVersionTimestamp;
                $oldPath         = $lastVersionPath . '/' . $fileName;
                $newPath         = $basePath . '/' . $resource->id . '/' . $check['timestamp'] . '/' . $fileName;

                if (!\is_file($newPath) || !$toCheck[$index]['handled']) {
                    continue;
                }

                $md5Old = $resource->hash;
                $md5New = \md5_file($newPath);

                if ($md5New === false) {
                    $md5New = '';
                }

                $hasDifferentHash = $md5Old !== $md5New;

                $difference = 0;

                // Is new file -> always different -> no content inspection required
                if ($hasDifferentHash && !\is_file($oldPath)) {
                    $difference = 1;

                    $hasDifferentHash = false;
                }

                // Different file hash -> content inspection required
                if ($hasDifferentHash) {
                    if (\in_array($extension, self::TEXT_RENDERABLE)) {
                        $contentOld = \Modules\Media\Controller\ApiController::loadFileContent($oldPath, $extension, 'txt', ['path' => $resource->xpath]);
                        $contentNew = \Modules\Media\Controller\ApiController::loadFileContent($newPath, $extension, 'txt', ['path' => $resource->xpath]);

                        $contentOld = \preg_replace('/(\ {2,}|\t)/', ' ', $contentOld);
                        $contentOld = \preg_replace('/(\s{2,})/', "\n", $contentOld ?? '');

                        $contentNew = \preg_replace('/(\ {2,}|\t)/', ' ', $contentNew);
                        $contentNew = \preg_replace('/(\s{2,})/', "\n", $contentNew ?? '');

                        $difference = 1;
                        if ($contentNew !== null && $contentOld !== null) {
                            // Calculate difference index
                            $difference = \levenshtein($contentOld, $contentNew);
                        }

                        $diffPath = \dirname($newPath) . '/_' . \basename($newPath);

                        \file_put_contents(
                            $diffPath,
                            \phpOMS\Utils\StringUtils::createDiffMarkup(
                                $contentOld ?? '',
                                $contentNew ?? '',
                                ' '
                            )
                        );
                    } elseif (\in_array($extension, self::IMG_RENDERABLE)) {
                        $diffPath = \dirname($newPath) . '/_' . \basename($newPath);

                        // Tool: software used is imagemagick
                        $comparison = SystemUtils::runProc(
                            OperatingSystem::getSystem() === SystemType::WIN ? 'compare.exe' : 'compare',
                            '-verbose -metric AE -compose src ' . $oldPath . ' ' . $newPath . ' ' . $diffPath,
                            false
                        );

                        // @todo allow $resource->path handling for x1/y1 -> x2/y2 coordinates

                        // Difference index is always 0/1. Comparing pixels is too slow and doesn't add much value
                        $comparisonTxt = \implode(' ', $comparison);

                        \preg_match('/ all:\s*(\d+)/', $comparisonTxt, $found);

                        $difference = ((int) $found[1]) < 10 ? 0 : 1;
                    } elseif ($extension === 'pdf') {
                        $diffPath = \dirname($newPath) . '/_' . \basename($newPath, '.pdf') . '.htm';

                        \file_put_contents(
                            $diffPath,
                            \phpOMS\Utils\StringUtils::createDiffMarkup(
                                \Modules\Media\Controller\ApiController::loadFileContent($oldPath, $extension),
                                \Modules\Media\Controller\ApiController::loadFileContent($newPath, $extension),
                                ' '
                            )
                        );

                        // @todo allow $resource->path handling for page/headline searches

                        $difference = 1;
                    } else {
                        // All other files always have a difference index of 0/1
                        $difference = 1;
                    }
                }

                $report               = new Report();
                $report->resource     = $resource->id;
                $report->versionPath  = (string) $check['timestamp'];
                $report->changeMetric = $difference;

                $old = clone $resource;

                if ($difference !== 0) {
                    $report->status = ReportStatus::CHANGE;

                    $resource->path            = (string) $resource->id;
                    $resource->lastVersionPath = (string) $check['timestamp'];
                    $resource->lastVersionDate = $report->createdAt;
                    $resource->hash            = $md5New;

                    $resource->reports[] = $report;

                    $changed[] = $resource;

                    Directory::copy($path, $basePath . '/' . $resource->id . '/' . $check['timestamp']);

                    // If is htm/html create image
                    if ($extension === 'htm') {
                        try {
                            // Tool: software used is wkhtmltopdf
                            SystemUtils::runProc(
                                OperatingSystem::getSystem() === SystemType::WIN ? 'wkhtmltoimage.exe' : 'wkhtmltoimage',
                                \escapeshellarg($resource->uri) . ' ' . \escapeshellarg($basePath . '/' . $resource->id . '/' . $check['timestamp'] . '/index.jpg'),
                                true
                            );
                        } catch (\Throwable $t) {
                            $this->app->logger->error(
                                \phpOMS\Log\FileLogger::MSG_FULL, [
                                    'message' => $t->getMessage(),
                                    'line'    => __LINE__,
                                    'file'    => self::class,
                                ]
                            );
                        }
                    }
                }

                $this->createModel($request->header->account, $report, ReportMapper::class, 'report', $request->getOrigin());
                $resource->checkedAt = $report->createdAt;
                $this->updateModel($request->header->account, $old, $resource, ResourceMapper::class, 'resource', $request->getOrigin());

                // @todo delete older history depending on plan

                unset($toCheck[$index]);
            }

            \usleep(100000); // 100ms delay to give the tools time to download between steps
        }

        // Kill running processes in x seconds that shouldn't be running any longer
        $time = \time();
        if (OperatingSystem::getSystem() === SystemType::LINUX) {
            SystemUtils::runProc('sleep', \max(0, $minTime - $time) . ' && pkill -9 -f wkhtmltoimage', true);
        } else {
            SystemUtils::runProc('timeout', '/t ' . \max(0, $minTime - $time) . ' > NUL && taskkill /F /IM wkhtmltoimage.exe', true);
        }

        Directory::delete($basePath . '/temp');

        return $changed;
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateResourceUpdate($request))) {
            $response->data['resource_create'] = new FormValidation($val);
            $response->header->status          = RequestStatusCode::R_400;

            return;
        }

        /** @var \Modules\OnlineResourceWatcher\Models\Resource $old */
        $old = ResourceMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        if ($old->owner->id !== $request->header->account) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidPermissionResponse($request, $response, []);

            return;
        }

        $new = $this->updateResourceFromRequest($request, clone $old);
        $this->updateModel($request->header->account, $old, $new, ResourceMapper::class, 'resource', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Validate resource create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateResourceUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create Resource from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Resource
     *
     * @since 1.0.0
     */
    private function updateResourceFromRequest(RequestAbstract $request, Resource $resource) : Resource
    {
        $resource->title = $request->getDataString('title') ?? '';
        $resource->uri   = $request->getDataString('uri') ?? '';

        return $resource;
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceGet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateResourceDelete($request))) {
            $response->data['resource_create'] = new FormValidation($val);
            $response->header->status          = RequestStatusCode::R_400;

            return;
        }

        /** @var \Modules\OnlineResourceWatcher\Models\Resource $resource */
        $resource = ResourceMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        if ($resource->owner->id !== $request->header->account) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidPermissionResponse($request, $response, []);

            return;
        }

        $this->deleteModel($request->header->account, $resource, ResourceMapper::class, 'resource', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $resource);
    }

    /**
     * Validate resource create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateResourceDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiInformCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateInformCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $resource = ResourceMapper::get()
            ->where('id', $request->getDataInt('resource'))
            ->execute();

        if ($resource->owner->id !== $request->header->account) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidPermissionResponse($request, $response, []);

            return;
        }

        $inform = $this->createInformFromRequest($request);
        $this->createModel($request->header->account, $inform, InformMapper::class, 'inform', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $inform);
    }

    /**
     * Validate inform create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateInformCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['email'] = (!$request->hasData('email') && !$request->hasData('account')))
            && ($val['resource'] = ($request->getDataInt('resource') === null))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create Inform from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Inform
     *
     * @since 1.0.0
     */
    private function createInformFromRequest(RequestAbstract $request) : Inform
    {
        $inform           = new Inform();
        $inform->account  = $request->getDataInt('account');
        $inform->email    = $request->getDataString('email') ?? '';
        $inform->resource = $request->getDataInt('resource') ?? 0;

        return $inform;
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiInformDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateInformDelete($request))) {
            $response->data['resource_create'] = new FormValidation($val);
            $response->header->status          = RequestStatusCode::R_400;

            return;
        }

        /** @var \Modules\OnlineResourceWatcher\Models\Inform $inform */
        $inform = InformMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        /** @var \Modules\OnlineResourceWatcher\Models\Resource $resource */
        $resource = ResourceMapper::get()
            ->where('id', $inform->resource)
            ->execute();

        if ($resource->owner->id !== $request->header->account) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidPermissionResponse($request, $response, []);

            return;
        }

        $this->deleteModel($request->header->account, $inform, InformMapper::class, 'inform', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $inform);
    }

    /**
     * Validate inform delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateInformDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))
        ) {
            return $val;
        }

        return [];
    }
}
