<?php

declare(strict_types=1);

namespace Models;

use phpOMS\Account\PermissionAbstract;
use phpOMS\Account\PermissionType;

class GroupPermission extends PermissionAbstract
{

    private int $group = 0;

    public function __construct(
        int $group = 0,
        int $unit = null,
        string $app = null,
        string $module = null,
        string $from = null,
        int $category = null,
        int $element = null,
        int $component = null,
        int $permission = PermissionType::NONE
    ) {
        $this->group = $group;
        parent::__construct($unit, $app, $module, $from, $category, $element, $component, $permission);
    }

    public function getGroup() : int
    {
        return $this->group;
    }
}
