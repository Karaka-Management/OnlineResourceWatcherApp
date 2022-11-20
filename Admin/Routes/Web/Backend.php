<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\OnlineResourceWatcher\Controller\BackendController;
use Modules\OnlineResourceWatcher\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/news/dashboard.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewOnlineResourceWatcherDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^.*/news/article.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewOnlineResourceWatcherArticle',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^.*/news/archive.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewOnlineResourceWatcherArchive',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^.*/news/draft/list.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewOnlineResourceWatcherDraftList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^.*/news/create.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewOnlineResourceWatcherCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^.*/news/edit.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewOnlineResourceWatcherEdit',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^.*/news/analysis.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewOnlineResourceWatcherAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
];
