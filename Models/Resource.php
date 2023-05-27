<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\OnlineResourceWatcher\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\OnlineResourceWatcher\Models;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\NullAccount;

/**
 * Resource class.
 *
 * @package Modules\OnlineResourceWatcher\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Resource implements \JsonSerializable
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Status.
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = ResourceStatus::ACTIVE;

    /**
     * Uri.
     *
     * @var string
     * @since 1.0.0
     */
    public string $uri = '';

    /**
     * Title.
     *
     * @var string
     * @since 1.0.0
     */
    public string $title = '';

    /**
     * Path.
     *
     * @var string
     * @since 1.0.0
     */
    public string $path = '';

    /**
     * Xpath.
     *
     * @var string
     * @since 1.0.0
     */
    public string $xpath = '';

    /**
     * Hash.
     *
     * @var string
     * @since 1.0.0
     */
    public string $hash = '';

    /**
     * Last version path.
     *
     * @var string
     * @since 1.0.0
     */
    public string $lastVersionPath = '';

    /**
     * Owner.
     *
     * @var Account
     * @since 1.0.0
     */
    public Account $owner;

    /**
     * Inform.
     *
     * @var Inform[]
     * @since 1.0.0
     */
    public array $inform = [];

    public array $reports = [];

    /**
     * Organization.
     *
     * The owner/creator of the resource can be different
     * from the group/organization this resource belongs to.
     *
     * @todo: consider to use groups instead of organizations?
     * groups would be better for internal purposes (e.g. departments) but accounts are better for external purposes (different customers)
     *
     * @var Account
     * @since 1.0.0
     */
    public Account $organization;

    /**
     * Last version date.
     *
     * @var null|\DateTimeImmutable
     * @since 1.0.0
     */
    public ?\DateTimeImmutable $lastVersionDate = null;

    /**
     * Last checked.
     *
     * @var null|\DateTimeImmutable
     * @since 1.0.0
     */
    public ?\DateTimeImmutable $checkedAt = null;

    /**
     * Created.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->owner        = new NullAccount();
        $this->organization = new NullAccount();
        $this->createdAt    = new \DateTimeImmutable('now');
    }

    /**
     * Get id
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
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'        => $this->id,
            'createdAt' => $this->createdAt,
            'owner'     => $this->owner,
        ];
    }

    /**
     * Get status
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
