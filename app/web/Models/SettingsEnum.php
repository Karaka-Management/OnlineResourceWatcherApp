<?php
declare(strict_types=1);

namespace Models;

use phpOMS\Stdlib\Base\Enum;

abstract class SettingsEnum extends Enum
{
    /* Logging settings */
    public const PASSWORD_PATTERN = '1000000001';

    public const LOGIN_TIMEOUT = '1000000002';

    public const PASSWORD_INTERVAL = '1000000003';

    public const PASSWORD_HISTORY = '1000000004';

    public const LOGIN_TRIES = '1000000005';

    public const LOGGING_STATUS = '1000000006';

    public const LOGGING_PATH = '1000000007';

    /* Organization settings */
    public const DEFAULT_ORGANIZATION = '1000000009';

    /* Login settings */
    public const LOGIN_FORGOTTEN_COUNT = '1000000010';

    public const LOGIN_FORGOTTEN_DATE = '1000000011';

    public const LOGIN_FORGOTTEN_TOKEN = '1000000012';

    public const LOGIN_STATUS = '1000000013';

    /* Localization settings */
    public const DEFAULT_LOCALIZATION = '1000000014';

    /* Mail settings */
    public const MAIL_SERVER_ADDR = '1000000015';

    public const MAIL_SERVER_TYPE = '1000000016';

    public const MAIL_SERVER_USER = '1000000017';

    public const MAIL_SERVER_PASS = '1000000018';

    public const MAIL_SERVER_CERT = '1000000019';

    public const MAIL_SERVER_KEY = '1000000020';

    public const MAIL_SERVER_KEYPASS = '1000000021';

    public const MAIL_SERVER_TLS = '1000000022';

    /* Cli settings */
    public const CLI_ACTIVE = '1000000023';

    /* Global default templates */
    public const DEFAULT_PDF_EXPORT_TEMPLATE = '1000000024';

    public const DEFAULT_CSV_EXPORT_TEMPLATE = '1000000025';

    public const DEFAULT_EXCEL_EXPORT_TEMPLATE = '1000000026';

    public const DEFAULT_WORD_EXPORT_TEMPLATE = '1000000027';

    public const DEFAULT_EMAIL_EXPORT_TEMPLATE = '1000000028';
}
