<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\OnlineResourceWatcher\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\OnlineResourceWatcher\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Permision state enum.
 *
 * @package Modules\OnlineResourceWatcher\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class PermissionCategory extends Enum
{
    public const RESOURCE = 1;

    public const REPORT = 2;

    public const UNIT = 3;
}
