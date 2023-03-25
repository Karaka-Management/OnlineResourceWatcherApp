<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\OnlineResourceWatcher\Controller\ApiController;
use Modules\OnlineResourceWatcher\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/orw/check -i all*$' => [
        [
            'dest'       => '\Modules\OnlineResourceWatcher\Controller\ApiController:apiCheckResources',
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::RESOURCE,
            ],
        ],
    ]
];