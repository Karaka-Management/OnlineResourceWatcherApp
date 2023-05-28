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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Inform mapper class.
 *
 * @package Modules\OnlineResourceWatcher\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Inform
 * @extends DataMapperFactory<T>
 */
final class InformMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'orw_resource_info_id'                 => ['name' => 'orw_resource_info_id',                'type' => 'int',               'internal' => 'id'],
        'orw_resource_info_mail'               => ['name' => 'orw_resource_info_mail',             'type' => 'string',            'internal' => 'email',],
        'orw_resource_info_account'            => ['name' => 'orw_resource_info_account',              'type' => 'int',            'internal' => 'account',],
        'orw_resource_info_resource'           => ['name' => 'orw_resource_info_resource',              'type' => 'int',            'internal' => 'resource',],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'orw_resource_info';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'orw_resource_info_id';
}
