<?php
declare(strict_types=1);

namespace Models;

final class AccountCredentialMapper extends AccountMapper
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'account_id'                  => ['name' => 'account_id',           'type' => 'int',      'internal' => 'id'],
        'account_status'              => ['name' => 'account_status',       'type' => 'int',      'internal' => 'status'],
        'account_type'                => ['name' => 'account_type',         'type' => 'int',      'internal' => 'type'],
        'account_login'               => ['name' => 'account_login',        'type' => 'string',   'internal' => 'login', 'autocomplete' => true],
        'account_name1'               => ['name' => 'account_name1',        'type' => 'string',   'internal' => 'name1', 'autocomplete' => true, 'annotations' => ['gdpr' => true]],
        'account_name2'               => ['name' => 'account_name2',        'type' => 'string',   'internal' => 'name2', 'autocomplete' => true, 'annotations' => ['gdpr' => true]],
        'account_name3'               => ['name' => 'account_name3',        'type' => 'string',   'internal' => 'name3', 'autocomplete' => true, 'annotations' => ['gdpr' => true]],
        'account_password'            => ['name' => 'account_password',     'type' => 'string',   'internal' => 'password', 'writeonly' => true],
        'account_password_temp'       => ['name' => 'account_password_temp',     'type' => 'string',   'internal' => 'tempPassword', 'writeonly' => true],
        'account_password_temp_limit' => ['name' => 'account_password_temp_limit',     'type' => 'DateTimeImmutable',   'internal' => 'tempPasswordLimit'],
        'account_email'               => ['name' => 'account_email',        'type' => 'string',   'internal' => 'email', 'autocomplete' => true, 'annotations' => ['gdpr' => true]],
        'account_tries'               => ['name' => 'account_tries',        'type' => 'int',      'internal' => 'tries'],
        'account_lactive'             => ['name' => 'account_lactive',      'type' => 'DateTime', 'internal' => 'lastActive'],
        'account_localization'        => ['name' => 'account_localization', 'type' => 'int',      'internal' => 'l11n'],
        'account_created_at'          => ['name' => 'account_created_at',   'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var string
     * @since 1.0.0
     */
    public const MODEL = Account::class;
}
