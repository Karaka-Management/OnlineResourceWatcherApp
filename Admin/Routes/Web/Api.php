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
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\OnlineResourceWatcher\Controller\ApiController;
use Modules\OnlineResourceWatcher\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/orw/resource(\?.*|$)' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiResourceCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiResourceUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiResourceGet',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiResourceDelete',
            'verb'       => RouteVerb::DELETE,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],

    '^.*/orw/resource/render.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiResourceRender',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
];
