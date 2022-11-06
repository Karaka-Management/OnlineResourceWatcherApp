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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

final class AuditMapper extends DataMapperFactory
{
    public const COLUMNS = [
        'auditor_audit_id'         => ['name' => 'auditor_audit_id',         'type' => 'int',               'internal' => 'id'],
        'auditor_audit_created_by' => ['name' => 'auditor_audit_created_by', 'type' => 'int',               'internal' => 'createdBy', 'readonly' => true],
        'auditor_audit_created_at' => ['name' => 'auditor_audit_created_at', 'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
        'auditor_audit_ip'         => ['name' => 'auditor_audit_ip',         'type' => 'int',               'internal' => 'ip', 'annotations' => ['gdpr' => true]],
        'auditor_audit_module'     => ['name' => 'auditor_audit_module',     'type' => 'string',            'internal' => 'module'],
        'auditor_audit_ref'        => ['name' => 'auditor_audit_ref',        'type' => 'string',            'internal' => 'ref'],
        'auditor_audit_type'       => ['name' => 'auditor_audit_type',       'type' => 'int',               'internal' => 'type'],
        'auditor_audit_trigger'    => ['name' => 'auditor_audit_trigger',    'type' => 'string',            'internal' => 'trigger'],
        'auditor_audit_content'    => ['name' => 'auditor_audit_content',    'type' => 'string',            'internal' => 'content'],
        'auditor_audit_old'        => ['name' => 'auditor_audit_old',        'type' => 'compress',          'internal' => 'old'],
        'auditor_audit_new'        => ['name' => 'auditor_audit_new',        'type' => 'compress',          'internal' => 'new'],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'createdBy' => [
            'mapper'   => AccountMapper::class,
            'external' => 'auditor_audit_created_by',
        ],
    ];

    public const MODEL = Audit::class;

    public const TABLE = 'auditor_audit';

    public const PRIMARYFIELD ='auditor_audit_id';

    public const CREATED_AT = 'auditor_audit_created_at';
}
