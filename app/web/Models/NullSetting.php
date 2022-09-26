<?php
declare(strict_types=1);

namespace Models;

final class NullSetting extends Setting
{

    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }
}
