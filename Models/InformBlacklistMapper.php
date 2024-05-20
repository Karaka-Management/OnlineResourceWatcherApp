<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\OnlineResourceWatcher\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\OnlineResourceWatcher\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Inform mapper class.
 *
 * @package Modules\OnlineResourceWatcher\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Inform
 * @extends DataMapperFactory<T>
 */
final class InformBlacklistMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'orw_resource_blacklist_id'   => ['name' => 'orw_resource_blacklist_id',                'type' => 'int',               'internal' => 'id'],
        'orw_resource_blacklist_mail' => ['name' => 'orw_resource_blacklist_mail',             'type' => 'string',            'internal' => 'email',],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'orw_resource_blacklist';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'orw_resource_blacklist_id';
}
