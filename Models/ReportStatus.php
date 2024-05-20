<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\OnlineResourceWatcher\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\OnlineResourceWatcher\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Event type enum.
 *
 * @package Modules\OnlineResourceWatcher\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class ReportStatus extends Enum
{
    public const ADDED = 1;

    public const NO_CHANGE = 2;

    public const CHANGE = 3;

    public const DOWNLOAD_ERROR = -1;
}
