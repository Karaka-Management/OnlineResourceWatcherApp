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
use Modules\OnlineResourceWatcher\Models\Report;
use Modules\OnlineResourceWatcher\Models\ReportMapper;
use Modules\OnlineResourceWatcher\Models\ReportStatus;
use Modules\OnlineResourceWatcher\Models\Resource;
use Modules\OnlineResourceWatcher\Models\ResourceMapper;
use Modules\OnlineResourceWatcher\Models\ResourceStatus;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\SystemUtils;
use phpOMS\Utils\ImageUtils;
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

        $path = '';
        if (\is_dir($basePath = __DIR__ . '/' . $resource->path . '/' . $resource->lastVersionPath)) {
            if (\is_file($basePath . '/index.htm')) {
                $path = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $resource->lastVersionPath . '/index.htm';
            } elseif (\is_file($basePath . '/index.html')) {
                $path = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $resource->lastVersionPath . '/index.html';
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
        }

        $internalRequest                  = new HttpRequest();
        $internalRequest->header->account = $request->header->account;
        $internalRequest->setData('path', $path);
        $this->app->moduleManager->get('Media')->apiMediaExport($internalRequest, $response, ['guard' => __DIR__ . '/../Files']);
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
            || ($val['uri'] = !$request->hasData('uri'))
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

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Resource', 'Resource successfully created', $resource);
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
        $this->checkResources();

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Resources', 'Resources were checked.', null);
    }

    public function checkResources(mixed $var = null) : array
    {
        $changed = [];

        /** @var Resource[] $resources */
        $resources = ResourceMapper::getAll()
            ->where('status', ResourceStatus::ACTIVE)
            ->execute();

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

            $path     .= 'temp/' . $resource->getId() . '/' . $timestamp;
            $toCheck[] = [
                'resource' => $resource,
                'timestamp' => $timestamp,
                'path' => $path,
                'handled' => false,
                'loop' => 0,
            ];

            SystemUtils::runProc(
                'wget',
                '--retry-connrefused --waitretry=1 --read-timeout=10 --timeout=10 --dns-timeout=10 -t 2 --quota=25m --adjust-extension --span-hosts --convert-links --page-requisites --no-directories --restrict-file-names=windows --no-parent ‐‐execute robots=off --limit-rate=5m --accept css,png,jpg,jpeg,gif,htm,html,txt,md,pdf,xls,xlsx,doc,docx --directory-prefix=' . $path . ' ‐‐output-document=index.html ' . $resource->uri,
                true
            );
        }

        // Check downloaded resources
        // @todo: this may not work correctly because the download runs async.
        $totalCount = \count($toCheck);
        $maxLoops   = (int) \min(60 * 10, $totalCount * 10 / 4);

        // @todo: the following code is INSANE, simplify!!!
        $baseLen = \strlen($basePath . '/temp');
        while (!empty($toCheck)) {
            foreach ($toCheck as $index => $check) {
                ++$toCheck[$index]['loop'];
                $resource = $check['resource'];

                // too many tries
                if ($check['loop'] > $maxLoops) {
                    $report              = new Report();
                    $report->resource    = $resource->getId();
                    $report->versionPath = (string) $check['timestamp'];
                    $report->status      = ReportStatus::DOWNLOAD_ERROR;

                    ReportMapper::create()->execute($report);

                    $resource->checkedAt = $report->createdAt;
                    ResourceMapper::update()->execute($resource);

                    unset($toCheck[$index]);

                    continue;
                }

                $path = $check['path'];
                if (!\is_dir($path)) {
                    // Either the download takes too long or the download failed!
                    continue;
                }

                $end = \stripos($path, '/', $baseLen + 1);
                $id  = (int) \substr($path, $baseLen + 1, $end - $baseLen - 1);

                // new resource
                if ($check['loop'] === $maxLoops && !\is_dir($basePath . '/' . $id)) {
                    $filesNew = \scandir($path);
                    if ($filesNew === false) {
                        $filesNew = [];
                    }

                    $fileName = '';
                    if (\in_array('index.htm', $filesNew) || \in_array('index.html', $filesNew)) {
                        $fileName = \in_array('index.htm', $filesNew) ? 'index.htm' : 'index.html';
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
                                $fileName = $file;
                                break;
                            }
                        }
                    }

                    $report              = new Report();
                    $report->resource    = $resource->getId();
                    $report->versionPath = (string) $check['timestamp'];

                    $hash = '';
                    if (!empty($fileName)) {
                        $report->status = ReportStatus::ADDED;
                        $hash           = \md5_file($path . '/' . $fileName);
                    } else {
                        $report->status = ReportStatus::DOWNLOAD_ERROR;
                    }

                    ReportMapper::create()->execute($report);

                    $resource->path            = (string) $resource->getId();
                    $resource->lastVersionPath = (string) $check['timestamp'];
                    $resource->lastVersionDate = $report->createdAt;
                    $resource->hash            = $hash == false ? '' : $hash;
                    $resource->checkedAt       = $report->createdAt;
                    ResourceMapper::update()->execute($resource);

                    Directory::copy($path, $basePath . '/' . $id . '/' . $check['timestamp']);
                    unset($toCheck[$index]);

                    continue;
                }

                if (!\is_dir($basePath . '/' . $id)) {
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

                $filesNew = \scandir($path);
                if ($filesNew === false) {
                    $filesNew = [];
                }

                // Using this because the index.htm gets created last and at the time of the check below it may not yet exist.
                $filesOld = \scandir($lastVersionPath);
                if ($filesOld === false) {
                    $filesOld = [];
                }

                $oldPath   = '';
                $newPath   = '';
                $extension = '';

                if (\in_array('index.htm', $filesOld) || \in_array('index.html', $filesOld)
                    || \in_array('index.htm', $filesNew) || \in_array('index.html', $filesNew)
                ) {
                    $extension = \in_array('index.htm', $filesOld) ? 'htm' : 'html';
                    $oldPath   = $lastVersionPath . '/index.' . $extension;
                    $newPath   = $path . '/index.' . $extension;

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
                            $oldPath   = $lastVersionPath . '/' . $file;
                            $newPath   = $path . '/' . $file;
                            $extension = \substr($file, \strripos($file, '.') + 1);

                            $toCheck[$index]['handled'] = true;

                            break;
                        }
                    }
                }

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
                    } elseif (\in_array($extension, ['png', 'jpg', 'jpeg', 'gif'])) {
                        $difference = ImageUtils::difference($oldPath, $newPath, $path . '/_' . \basename($newPath), 0);
                    }
                }

                $report               = new Report();
                $report->resource     = $resource->getId();
                $report->versionPath  = (string) $check['timestamp'];
                $report->changeMetric = $difference;

                if ($difference !== 0) {
                    $report->status = ReportStatus::CHANGE;

                    $resource->path            = (string) $resource->getId();
                    $resource->lastVersionPath = (string) $check['timestamp'];
                    $resource->lastVersionDate = $report->createdAt;
                    $resource->hash            = $md5New;

                    $changed[] = $report;

                    Directory::copy($path, $basePath . '/' . $id . '/' . $check['timestamp']);
                }

                ReportMapper::create()->execute($report);

                $resource->checkedAt = $report->createdAt;
                ResourceMapper::update()->execute($resource);

                Directory::delete($basePath . '/temp/' . $id);

                // @todo: delete older history depending on plan
                // @todo: inform users

                unset($toCheck[$index]);
            }

            \sleep(1);
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
    }
}
