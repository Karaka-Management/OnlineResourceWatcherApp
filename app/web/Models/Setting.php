<?php

declare(strict_types=1);

namespace Models;

class Setting
{

    protected int $id = 0;

    public string $name = '';

    public string $content = '';

    public string $pattern = '';

    public ?int $app = null;

    public ?string $module = null;

    public ?int $group = null;

    public ?int $account = null;

    public function getId() : int
    {
        return $this->id;
    }

    public function with(
        int $id = 0,
        string $name = '',
        string $content = '',
        string $pattern = '',
        int $app = null,
        string $module = null,
        int $group = null,
        int $account = null
    ) : self
    {
        $this->id      = $id;
        $this->name    = $name;
        $this->content = $content;
        $this->pattern = $pattern;
        $this->app     = $app;
        $this->module  = $module;
        $this->group   = $group;
        $this->account = $account;

        return $this;
    }

    public function __construct(
        int $id = 0,
        string $name = '',
        string $content = '',
        string $pattern = '',
        int $app = null,
        string $module = null,
        int $group = null,
        int $account = null
    ) {
        $this->id      = $id;
        $this->name    = $name;
        $this->content = $content;
        $this->pattern = $pattern;
        $this->app     = $app;
        $this->module  = $module;
        $this->group   = $group;
        $this->account = $account;
    }
}
