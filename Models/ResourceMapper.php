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

use Modules\Admin\Models\AccountMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Resource mapper class.
 *
 * @package Modules\OnlineResourceWatcher\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Resource
 * @extends DataMapperFactory<T>
 */
final class ResourceMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'orw_resource_id'                => ['name' => 'orw_resource_id',                'type' => 'int',               'internal' => 'id'],
        'orw_resource_title'             => ['name' => 'orw_resource_title',             'type' => 'string',            'internal' => 'title',],
        'orw_resource_path'              => ['name' => 'orw_resource_path',              'type' => 'string',            'internal' => 'path',],
        'orw_resource_status'            => ['name' => 'orw_resource_status',            'type' => 'int',               'internal' => 'status',],
        'orw_resource_uri'               => ['name' => 'orw_resource_uri',               'type' => 'string',            'internal' => 'uri',],
        'orw_resource_xpath'             => ['name' => 'orw_resource_xpath',             'type' => 'string',            'internal' => 'xpath',],
        'orw_resource_hash'              => ['name' => 'orw_resource_hash',              'type' => 'string',            'internal' => 'hash',],
        'orw_resource_last_version_path' => ['name' => 'orw_resource_last_version_path', 'type' => 'string',            'internal' => 'lastVersionPath',],
        'orw_resource_last_version_date' => ['name' => 'orw_resource_last_version_date', 'type' => 'DateTimeImmutable', 'internal' => 'lastVersionDate',],
        'orw_resource_checked_at'        => ['name' => 'orw_resource_checked_at',        'type' => 'DateTimeImmutable', 'internal' => 'checkedAt',],
        'orw_resource_owner'             => ['name' => 'orw_resource_owner',             'type' => 'int',               'internal' => 'owner',],
        'orw_resource_organization'      => ['name' => 'orw_resource_organization',      'type' => 'int',               'internal' => 'organization',],
        'orw_resource_created_at'        => ['name' => 'orw_resource_created_at',        'type' => 'DateTimeImmutable', 'internal' => 'createdAt',],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'owner' => [
            'mapper'   => AccountMapper::class,
            'external' => 'orw_resource_owner',
        ],
        'organization' => [
            'mapper'   => AccountMapper::class,
            'external' => 'orw_resource_organization',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'orw_resource';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'orw_resource_id';
}
