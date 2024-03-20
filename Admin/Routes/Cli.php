<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
    ],
];
