<?php
declare(strict_types=1);

namespace Models;

final class NullAccount extends Account
{
    public function __construct(int $id = 0)
    {
        parent::__construct();
        $this->id = $id;
    }
}
