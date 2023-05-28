<?php
/**
 * Karaka
 *
 * PHP Version 8.1
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
use Modules\Admin\Models\SettingsEnum;
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
use phpOMS\System\File\Local\Directory;
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
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceRender(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\OnlineResourceWatcher\Models\Resource $resource */
        $resource = ResourceMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

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
            || ($val['uri'] = (!$request->hasData('uri') || \filter_var($request->getDataString('uri'), \FILTER_VALIDATE_URL) === false))
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateResourceCreate($request))) {
            $response->set('resource_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $resource = $this->createResourceFromRequest($request);
        $this->createModel($request->header->account, $resource, ResourceMapper::class, 'resource', $request->getOrigin());

        $this->checkResources($request, [$resource]);

        $this->createStandardCreateResponse($request, $response, $resource);
    }

    /**
     * Method to create news article from request.
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

        // @todo: check if user is part of organization below AND has free resources to add!!!
        $resource->organization = new NullAccount(
            !$request->hasData('organization')
                ? 1
                : (int) ($request->getData('organization'))
            );

        return $resource;
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCheckResources(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Resource[] $resources */
        $resources = ResourceMapper::getAll()
            ->with('owner')
            ->with('owner/l11n')
            ->with('inform')
            ->where('status', ResourceStatus::ACTIVE)
            ->where('checkedAt', (new \DateTime('now'))->sub(new \DateInterval('PT12H')), '<')
            ->execute();

        $this->checkResources($request, $resources);
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Resources', 'Resources were checked.', null);
    }

    /**
     * Inform users about changed resources
     *
     * @param mixed $var Generic variable
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function informUsers(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $dateTime = new \DateTime('now');
        $dateTime = $dateTime->modify('-1 hour');

        $reports = ReportMapper::getAll()
            ->where('status', ReportStatus::CHANGE)
            ->where('createdAt', $dateTime, '>=')
            ->execute();

        foreach ($reports as $report) {
            // @todo: get templates
            // @todo: get users to inform
            // @todo: inform users
        }
    }

    /**
     * Checks resources for changes
     *
     * @param mixed $var Generic variable
     *
     * @return array
     *
     * @since 1.0.0
     * @todo: implement iterative approach where you can define a "offset" and "limit" to check only a few resources at a time
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

            $path     .= 'temp/' . $resource->id . '/' . $timestamp;
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
                    '--retry-connrefused --waitretry=1 --read-timeout=10 --timeout=10 --dns-timeout=10 -t 2 --quota=25m --adjust-extension --span-hosts --convert-links --no-directories --restrict-file-names=windows --no-parent ‐‐execute robots=off --limit-rate=5m -U mozilla --accept css,png,jpg,jpeg,gif,htm,html,txt,md,pdf,xls,xlsx,doc,docx --directory-prefix=' . \escapeshellarg($path) . ' ‐‐output-document=index.htm ' . \escapeshellarg($resource->uri),
                    true
                );
            } catch (\Throwable $t) {
                $this->app->logger->error($t->getMessage());
            }
        }

        $handler = $this->app->moduleManager->get('Admin', 'Api')->setUpServerMailHandler();

        $emailSettings = $this->app->appSettings->get(
            names: SettingsEnum::MAIL_SERVER_ADDR,
            module: 'OnlineResourceWatcher'
        );

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
            ->execute();

        // Check downloaded resources
        $totalCount = \count($toCheck);
        $maxLoops   = (int) \min(60 * 10, $totalCount * 10 / 4);
        $startTime  = \time();
        $minTime    = $startTime + \min(10 * $totalCount, 60 * 5); // At least run 10 seconds per element
        $maxTime    = $startTime + \min(60 * $totalCount, 60 * 60 * 3); // At most run 60 seconds per element

        $baseLen = \strlen($basePath . '/temp');
        while (!empty($toCheck)) {
            $time = \time();

            foreach ($toCheck as $index => $check) {
                ++$toCheck[$index]['loop'];

                /** @var Resource $resource */
                $resource = $check['resource'];

                // too many tries
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

                    continue;
                }

                $path = $check['path'];
                if (!\is_dir($path)) {
                    // Either the download takes too long or the download failed!
                    // Let's go to the next element and re-check later on.
                    continue;
                }

                $end = \stripos($path, '/', $baseLen + 1);
                $id  = (int) \substr($path, $baseLen + 1, $end - $baseLen - 1);

                // new resource
                $filesNew = \scandir($path);
                if ($filesNew === false) {
                    $filesNew = [];
                }

                $oldPath   = '';
                $newPath   = '';
                $extension = '';

                $fileName = '';
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
                        if (StringUtils::endsWith($file, '.png')
                            || StringUtils::endsWith($file, '.jpg')
                            || StringUtils::endsWith($file, '.jpeg')
                            || StringUtils::endsWith($file, '.gif')
                            || StringUtils::endsWith($file, '.pdf')
                            || StringUtils::endsWith($file, '.doc')
                            || StringUtils::endsWith($file, '.docx')
                            || StringUtils::endsWith($file, '.xls')
                            || StringUtils::endsWith($file, '.xlsx')
                            || StringUtils::endsWith($file, '.md')
                            || StringUtils::endsWith($file, '.txt')
                        ) {
                            $fileName                   = $file;
                            $extension                  = \substr($file, \strripos($file, '.') + 1);
                            $toCheck[$index]['handled'] = true;

                            break;
                        }
                    }
                }

                // Is new resource
                if (!\is_dir($basePath . '/' . $id)) {
                    $report              = new Report();
                    $report->resource    = $resource->id;
                    $report->versionPath = (string) $check['timestamp'];

                    $hash = '';
                    if (!empty($fileName)) {
                        $report->status = ReportStatus::ADDED;
                        $hash           = \md5_file($path . '/' . $fileName);
                    } else {
                        $report->status = ReportStatus::DOWNLOAD_ERROR;
                    }

                    $this->createModel($request->header->account, $report, ReportMapper::class, 'report', $request->getOrigin());
                    $old                       = clone $resource;
                    $resource->path            = (string) $resource->id;
                    $resource->lastVersionPath = (string) $check['timestamp'];
                    $resource->lastVersionDate = $report->createdAt;
                    $resource->hash            = $hash == false ? '' : $hash;
                    $resource->checkedAt       = $report->createdAt;
                    $this->updateModel($request->header->account, $old, $resource, ResourceMapper::class, 'resource', $request->getOrigin());

                    Directory::copy($path, $basePath . '/' . $id . '/' . $check['timestamp']);
                    unset($toCheck[$index]);

                    if ($extension === 'htm') {
                        try {
                            SystemUtils::runProc(
                                OperatingSystem::getSystem() === SystemType::WIN ? 'wkhtmltoimage.exe' : 'wkhtmltoimage',
                                \escapeshellarg($resource->uri) . ' ' . \escapeshellarg($basePath . '/' . $id . '/' . $check['timestamp'] . '/index.jpg'),
                                true
                            );
                        } catch (\Throwable $t) {
                            $this->app->logger->error($t->getMessage());
                        }
                    }

                    continue;
                }

                // existing resource
                $resourcePaths = \scandir($basePath . '/' . $id);
                if ($resourcePaths === false) {
                    $resourcePaths = [];
                }

                \natsort($resourcePaths);

                $lastVersionTimestamp = \end($resourcePaths);
                if ($lastVersionTimestamp === '.' || $lastVersionTimestamp === '..') {
                    $lastVersionTimestamp = \reset($resourcePaths);

                    if ($lastVersionTimestamp === '.' || $lastVersionTimestamp === '..') {
                        Directory::delete($basePath . '/' . $id);
                    }
                }

                $lastVersionPath = $basePath . '/' . $id . '/' . $lastVersionTimestamp;
                $oldPath         = $lastVersionPath . '/' . $fileName;
                $newPath         = $path . '/' . $fileName;

                if (!\is_file($newPath) || !$toCheck[$index]['handled']) {
                    continue;
                }

                $md5Old = $resource->hash;
                $md5New = \md5_file($newPath);

                if ($md5New === false) {
                    $md5New = '';
                }

                $hasDifferentHash = $md5Old !== $md5New;

                // @todo: check if old path exists and if not, don't calculate a diff

                $difference = 0;
                if ($hasDifferentHash) {
                    if (\in_array($extension, ['md', 'txt', 'doc', 'docx', 'pdf', 'xls', 'xlsx'])) {
                        $contentOld = \Modules\Media\Controller\ApiController::loadFileContent($oldPath, $extension);
                        $contentNew = \Modules\Media\Controller\ApiController::loadFileContent($newPath, $extension);

                        $difference = \levenshtein($contentOld, $contentNew);

                        // Handle xpath
                        if ($difference > 0
                            && $extension === 'htm'
                            && $resource->path !== ''
                        ) {
                            $xmlOld = new \DOMDocument();
                            $xmlNew = new \DOMDocument();

                            $xmlOld->loadHtml($contentOld);
                            $xmlNew->loadHtml($contentNew);

                            $xpathOld = new \DOMXpath($xmlOld);
                            $xpathNew = new \DOMXpath($xmlNew);

                            $elementsOld = $xpathOld->query($resource->path);
                            $elementsNew = $xpathNew->query($resource->path);

                            $subcontentOld = '';
                            foreach ($elementsOld as $node) {
                                foreach ($node->childNodes as $child) {
                                    $subcontentOld .= $xmlOld->saveXML($child);
                                }
                            }

                            $subcontentNew = '';
                            foreach ($elementsNew as $node) {
                                foreach ($node->childNodes as $child) {
                                    $subcontentNew .= $xmlNew->saveXML($child);
                                }
                            }

                            $difference = \levenshtein($subcontentOld, $subcontentNew);
                        }
                    } elseif (\in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'svg'])) {
                        $difference = 1; //ImageUtils::difference($oldPath, $newPath, $path . '/_' . \basename($newPath), 0); // too slow
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

                    $changed[] = $report;

                    Directory::copy($path, $basePath . '/' . $id . '/' . $check['timestamp']);

                    // If is htm/html create image
                    if ($extension === 'htm') {
                        try {
                            SystemUtils::runProc(
                                OperatingSystem::getSystem() === SystemType::WIN ? 'wkhtmltoimage.exe' : 'wkhtmltoimage',
                                \escapeshellarg($resource->uri) . ' ' . \escapeshellarg($basePath . '/' . $id . '/' . $check['timestamp'] . '/index.jpg'),
                                true
                            );
                        } catch (\Throwable $t) {
                            $this->app->logger->error($t->getMessage());
                        }
                    }

                    // @todo: move to informUsers function
                    $owner              = new Inform();
                    $owner->email       = $resource->owner->getEmail();
                    $resource->inform[] = $owner;

                    foreach ($resource->inform as $inform) {
                        foreach ($blacklist as $block) {
                            if (\stripos($inform->email, $block->email) !== false) {
                                continue 2;
                            }
                        }

                        $mail = clone $baseEmail;
                        $mail->setFrom($emailSettings->content);

                        $mailL11n = $baseEmail->getL11nByLanguage($resource->owner->l11n->getLanguage());
                        if ($mailL11n->id === 0) {
                            $mailL11n = $baseEmail->getL11nByLanguage($this->app->l11nServer->getLanguage());
                        }

                        if ($mailL11n->id === 0) {
                            $mailL11n = $baseEmail->getL11nByLanguage('en');
                        }

                        $mail->subject = $mailL11n->subject;

                        $mail->body = \str_replace(
                            [
                                '{resource.id}',
                                '{email}',
                                '{resource.url}',
                                '{owner_email}',
                            ],
                            [
                                $resource->id,
                                $inform->email,
                                $resource->uri,
                                $resource->owner->getEmail(),
                            ],
                            $mailL11n->body
                        );
                        $mail->msgHTML($mail->body);

                        $mail->bodyAlt = \str_replace(
                            [
                                '{resource.id}',
                                '{email}',
                                '{resource.url}',
                                '{owner_email}',
                            ],
                            [
                                $resource->id,
                                $inform->email,
                                $resource->uri,
                                $resource->owner->getEmail(),
                            ],
                            $mailL11n->bodyAlt
                        );

                        $mail->addTo($inform->email);
                        $handler->send($mail);
                    }
                }

                $this->createModel($request->header->account, $report, ReportMapper::class, 'report', $request->getOrigin());
                $resource->checkedAt = $report->createdAt;
                $this->updateModel($request->header->account, $old, $resource, ResourceMapper::class, 'resource', $request->getOrigin());

                // Directory::delete($basePath . '/temp/' . $id);

                // @todo: delete older history depending on plan

                unset($toCheck[$index]);
            }

            \usleep(100000); // 100ms
        }

        // make sure that no wkhtmltoimage processes are running
        if (OperatingSystem::getSystem() === SystemType::LINUX) {
            SystemUtils::runProc('pkill', '-f wkhtmltoimage', true);
        } else {
            SystemUtils::runProc('taskkill', '/F /IM wkhtmltoimage.exe', true);
        }

        Directory::delete($basePath . '/temp');

        return $changed;
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateResourceUpdate($request))) {
            $response->set('resource_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        /** @var \Modules\OnlineResourceWatcher\Models\Resource $old */
        $old = ResourceMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        if ($old->owner->id !== $request->header->account) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidPermissionResponse($request, $response, null);

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
     * Method to create news article from request.
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiResourceDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateResourceDelete($request))) {
            $response->set('resource_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        /** @var \Modules\OnlineResourceWatcher\Models\Resource $resource */
        $resource = ResourceMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        if ($resource->owner->id !== $request->header->account) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidPermissionResponse($request, $response, null);

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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiInformCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateInformCreate($request))) {
            $response->set('resource_inform_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $resource = ResourceMapper::get()
            ->where('id', $request->getDataInt('resource'))
            ->execute();

        if ($resource->owner->id !== $request->header->account) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidPermissionResponse($request, $response, null);

            return;
        }

        $resource = $this->createInformFromRequest($request);
        $this->createModel($request->header->account, $resource, InformMapper::class, 'resource', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $resource);
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
     * Method to create news article from request.
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
        $inform->email    = $request->getDataString('email');
        $inform->resource = $request->getDataInt('resource');

        return $inform;
    }

    /**
     * Api method to create resource
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiInformDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateInformDelete($request))) {
            $response->set('resource_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

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
            $this->createInvalidPermissionResponse($request, $response, null);

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
