<?php
declare(strict_types=1);

namespace Models;

class Account extends \phpOMS\Account\Account
{
    public int $tries = 0;

    public string $tempPassword = '';

    public array $parents = [];

    public ?\DateTimeImmutable $tempPasswordLimit = null;
}
