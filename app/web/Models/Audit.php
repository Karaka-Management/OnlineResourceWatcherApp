<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Auditor\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Models;

use phpOMS\Account\Account;

/**
 * Audit class.
 *
 * @package Modules\Auditor\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
class Audit
{
    /**
     * Audit id.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Audit type.
     *
     * @var int
     * @since 1.0.0
     */
    public int $type;

    /**
     * Audit trigger.
     *
     * @var string
     * @since 1.0.0
     */
    public string $trigger;

    /**
     * Audit module.
     *
     * @var null|string
     * @since 1.0.0
     */
    public ?string $module;

    /**
     * Audit reference.
     *
     * This could be used to reference other model ids
     *
     * @var null|string
     * @since 1.0.0
     */
    public ?string $ref;

    /**
     * Audit content.
     *
     * Additional audit information
     *
     * @var null|string
     * @since 1.0.0
     */
    public ?string $content;

    /**
     * Old value.
     *
     * @var null|string
     * @since 1.0.0
     */
    public ?string $old;

    /**
     * New value.
     *
     * @var null|string
     * @since 1.0.0
     */
    public ?string $new;

    /**
     * Account.
     *
     * @var Account
     * @since 1.0.0
     */
    public Account $createdBy;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Ip of creator.
     *
     * @var int
     * @since 1.0.0
     */
    private int $ip = 0;

    /**
     * Constructor.
     *
     * @param Account     $account Account of the creator
     * @param null|string $old     Old value
     * @param null|string $new     New value
     * @param int         $type    Type of the audit
     * @param string      $trigger Subtype of the audit
     * @param null|string $module  Module id
     * @param null|string $ref     Dynamic reference to model
     * @param null|string $content Additional audit information
     * @param int         $ip      Ip
     *
     * @since 1.0.0
     */
    public function __construct(
        Account $account = null,
        string $old = null,
        string $new = null,
        int $type = 0,
        string $trigger = '',
        string $module = null,
        string $ref = null,
        string $content = null,
        int $ip = 0
    ) {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->createdBy = $account ?? new NullAccount();
        $this->old       = $old;
        $this->new       = $new;
        $this->type      = $type;
        $this->trigger   = $trigger;
        $this->module    = $module;
        $this->ref       = $ref;
        $this->content   = $content;
        $this->ip        = $ip;
    }

    /**
     * Get id.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get type.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * Get subtype.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getTrigger() : string
    {
        return $this->trigger;
    }

    /**
     * Get Module.
     *
     * @return null|string
     *
     * @since 1.0.0
     */
    public function getModule() : ?string
    {
        return $this->module;
    }

    /**
     * Get reference.
     *
     * If existing this can be a reference to another model
     *
     * @return null|string
     *
     * @since 1.0.0
     */
    public function getRef() : ?string
    {
        return $this->ref;
    }

    /**
     * Get content.
     *
     * @return null|string
     *
     * @since 1.0.0
     */
    public function getContent() : ?string
    {
        return $this->content;
    }

    /**
     * Get old value.
     *
     * @return null|string
     *
     * @since 1.0.0
     */
    public function getOld() : ?string
    {
        return $this->old;
    }

    /**
     * Get new value.
     *
     * @return null|string
     *
     * @since 1.0.0
     */
    public function getNew() : ?string
    {
        return $this->new;
    }

    /**
     * Get ip.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getIp() : int
    {
        return $this->ip;
    }
}
