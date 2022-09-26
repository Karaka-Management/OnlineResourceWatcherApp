<?php

declare(strict_types=1);

namespace Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

final class GroupPermissionMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'group_permission_id'             => ['name' => 'group_permission_id',         'type' => 'int',    'internal' => 'id'],
        'group_permission_group'          => ['name' => 'group_permission_group',      'type' => 'int',    'internal' => 'group'],
        'group_permission_unit'           => ['name' => 'group_permission_unit',       'type' => 'int',    'internal' => 'unit'],
        'group_permission_app'            => ['name' => 'group_permission_app',        'type' => 'string', 'internal' => 'app'],
        'group_permission_module'         => ['name' => 'group_permission_module',     'type' => 'string', 'internal' => 'module'],
        'group_permission_from'           => ['name' => 'group_permission_from',       'type' => 'string',    'internal' => 'from'],
        'group_permission_category'       => ['name' => 'group_permission_category',       'type' => 'int',    'internal' => 'category'],
        'group_permission_element'        => ['name' => 'group_permission_element',    'type' => 'int',    'internal' => 'element'],
        'group_permission_component'      => ['name' => 'group_permission_component',  'type' => 'int',    'internal' => 'component'],
        'group_permission_hasread'        => ['name' => 'group_permission_hasread', 'type' => 'bool',    'internal' => 'hasRead'],
        'group_permission_hascreate'      => ['name' => 'group_permission_hascreate', 'type' => 'bool',    'internal' => 'hasCreate'],
        'group_permission_hasmodify'      => ['name' => 'group_permission_hasmodify', 'type' => 'bool',    'internal' => 'hasModify'],
        'group_permission_hasdelete'      => ['name' => 'group_permission_hasdelete', 'type' => 'bool',    'internal' => 'hasDelete'],
        'group_permission_haspermission'  => ['name' => 'group_permission_haspermission', 'type' => 'bool',    'internal' => 'hasPermission'],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var string
     * @since 1.0.0
     */
    public const MODEL = GroupPermission::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'group_permission';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='group_permission_id';
}
