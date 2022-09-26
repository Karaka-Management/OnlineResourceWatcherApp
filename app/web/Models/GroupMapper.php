<?php
declare(strict_types=1);

namespace Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Database\Query\Builder;

final class GroupMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'group_id'       => ['name' => 'group_id',       'type' => 'int',      'internal' => 'id'],
        'group_name'     => ['name' => 'group_name',     'type' => 'string',   'internal' => 'name', 'autocomplete' => true],
        'group_status'   => ['name' => 'group_status',   'type' => 'int',      'internal' => 'status'],
        'group_desc'     => ['name' => 'group_desc',     'type' => 'string',   'internal' => 'description'],
        'group_desc_raw' => ['name' => 'group_desc_raw', 'type' => 'string',   'internal' => 'descriptionRaw'],
        'group_created'  => ['name' => 'group_created',  'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var string
     * @since 1.0.0
     */
    public const MODEL = Group::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'group';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='group_id';

    /**
     * Created at column
     *
     * @var string
     * @since 1.0.0
     */
    public const CREATED_AT = 'group_created';

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'accounts' => [
            'mapper'   => AccountMapper::class,
            'table'    => 'account_group',
            'external' => 'account_group_account',
            'self'     => 'account_group_group',
        ],
        'permissions' => [
            'mapper'   => GroupPermissionMapper::class,
            'table'    => 'group_permission',
            'external' => null,
            'self'     => 'group_permission_group',
        ],
    ];

    /**
     * Get groups which reference a certain module
     *
     * @param string $module Module
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function getPermissionForModule(string $module) : array
    {
        $query = self::getQuery();
        $query->innerJoin(GroupPermissionMapper::TABLE)
            ->on(self::TABLE . '_d1.group_id', '=', GroupPermissionMapper::TABLE . '.group_permission_group')
            ->where(GroupPermissionMapper::TABLE . '.group_permission_module', '=', $module);

        return self::getAll()->execute($query);
    }

    /**
     * Count the number of group members
     *
     * @param int $group Group to inspect (0 = all groups)
     *
     * @return array<string, int>
     *
     * @since 1.0.0
     */
    public static function countMembers(int $group = 0) : array
    {
        $query = new Builder(self::$db);
        $query->select(self::HAS_MANY['accounts']['self'])
            ->select('COUNT(' . self::HAS_MANY['accounts']['external'] . ')')
            ->from(self::HAS_MANY['accounts']['table'])
            ->groupBy(self::HAS_MANY['accounts']['self']);

        if ($group !== 0) {
            $query->where(self::HAS_MANY['accounts']['self'], '=', $group);
        }

        $result = $query->execute()?->fetchAll(\PDO::FETCH_KEY_PAIR);

        return $result === null ? [] : $result;
    }
}
