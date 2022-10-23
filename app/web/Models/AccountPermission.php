<?php
declare(strict_types=1);

namespace Modules\Admin\Models;

use phpOMS\Account\PermissionAbstract;
use phpOMS\Account\PermissionType;

class AccountPermission extends PermissionAbstract
{
    private int $account = 0;

    public function __construct(
        int $account = 0,
        int $unit = null,
        string $app = null,
        string $module = null,
        string $from = null,
        int $category = null,
        int $element = null,
        int $component = null,
        int $permission = PermissionType::NONE
    ) {
        $this->account = $account;
        parent::__construct($unit, $app, $module, $from, $category, $element, $component, $permission);
    }

    public function getAccount() : int
    {
        return $this->account;
    }
}
