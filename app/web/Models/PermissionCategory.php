<?php
declare(strict_types=1);

namespace Models;

use phpOMS\Stdlib\Base\Enum;

abstract class PermissionCategory extends Enum
{
    public const SETTINGS = 1;

    public const ACCOUNT = 2;

    public const GROUP = 3;

    public const MODULE = 4;

    public const LOG = 5;

    public const ROUTE = 6;

    public const APP = 7;

    public const ACCOUNT_SETTINGS = 8;

    public const SEARCH = 9;

    public const API = 9;
}
