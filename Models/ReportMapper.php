<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\OnlineResourceWatcher\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
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
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ReportMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'orw_resource_report_id'                => ['name' => 'orw_resource_report_id',                'type' => 'int',               'internal' => 'id'],
        'orw_resource_report_status'            => ['name' => 'orw_resource_report_status',            'type' => 'int',               'internal' => 'status',],
        'orw_resource_report_metric'             => ['name' => 'orw_resource_report_metric',             'type' => 'int',            'internal' => 'changeMetric',],
        'orw_resource_report_path'              => ['name' => 'orw_resource_report_path',              'type' => 'string',            'internal' => 'versionPath',],
        'orw_resource_report_change'             => ['name' => 'orw_resource_report_change',             'type' => 'string',            'internal' => 'change',],
        'orw_resource_report_resource'      => ['name' => 'orw_resource_report_resource',      'type' => 'int',               'internal' => 'resource',],
        'orw_resource_report_created_at'        => ['name' => 'orw_resource_report_created_at',        'type' => 'DateTimeImmutable', 'internal' => 'createdAt',],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'orw_resource_report';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'orw_resource_report_id';
}
