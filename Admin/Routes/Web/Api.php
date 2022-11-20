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

use Modules\OnlineResourceWatcher\Controller\ApiController;
use Modules\OnlineResourceWatcher\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/news.*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiOnlineResourceWatcherCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiOnlineResourceWatcherUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiOnlineResourceWatcherGet',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiOnlineResourceWatcherDelete',
            'verb'       => RouteVerb::DELETE,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ],
];
