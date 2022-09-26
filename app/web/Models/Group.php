<?php
declare(strict_types=1);

namespace Models;

class Group extends \phpOMS\Account\Group
{
    public \DateTimeImmutable $createdAt;

    public Account $createdBy;

    public string $descriptionRaw = '';

    protected array $accounts = [];

    public function __construct(string $name = '')
    {
        $this->createdBy = new NullAccount();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->name      = $name;
    }

    public function getAccounts() : array
    {
        return $this->accounts;
    }
}
