<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\OnlineResourceWatcher\Controller\BackendController;
use Modules\OnlineResourceWatcher\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/orw/resource/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewResourceList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^/orw/resource(\?.*$|$)' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewResource',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^/orw/resource/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewResourceCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^/orw/resource/report/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewReportList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
    '^/orw/resource/report(\?.*$|$)' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\BackendController:viewReport',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
];
