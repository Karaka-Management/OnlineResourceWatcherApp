<?php

/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */

declare(strict_types=1);

namespace Install;

use Models\Account;
use Models\AccountCredentialMapper;
use Models\Group;
use Models\GroupMapper;
use Models\GroupPermission;
use Models\GroupPermissionMapper;
use Models\PermissionCategory;
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Account\GroupStatus;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\DataStorage\Database\Connection\ConnectionAbstract;
use phpOMS\DataStorage\Database\Connection\ConnectionFactory;
use phpOMS\DataStorage\Database\Connection\SQLiteConnection;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Schema\Builder as SchemaBuilder;
use phpOMS\Message\RequestAbstract;

abstract class InstallAbstract extends ApplicationAbstract
{
    protected function setupHandlers(): void
    {
        \set_exception_handler(['\phpOMS\UnhandledHandler', 'exceptionHandler']);
        \set_error_handler(['\phpOMS\UnhandledHandler', 'errorHandler']);
        \register_shutdown_function(['\phpOMS\UnhandledHandler', 'shutdownHandler']);
        \mb_internal_encoding('UTF-8');
    }

    protected static function clearOld(): void
    {
        \file_put_contents(__DIR__ . '/../Routes.php', '<?php return [];');
        \file_put_contents(__DIR__ . '/../Hooks.php', '<?php return [];');
    }

    protected static function hasPhpExtensions(): bool
    {
        return \extension_loaded('pdo')
            && \extension_loaded('mbstring');
    }

    protected static function testDbConnection(RequestAbstract $request): bool
    {
        return true;
    }

    protected static function setupDatabaseConnection(RequestAbstract $request): ConnectionAbstract
    {
        return ConnectionFactory::create([
            'db'       => (string) $request->getData('dbtype'),
            'host'     => (string) $request->getData('dbhost'),
            'port'     => (int) $request->getData('dbport'),
            'database' => (string) $request->getData('dbname'),
            'login'    => (string) $request->getData('schemauser'),
            'password' => (string) $request->getData('schemapassword'),
        ]);
    }

    protected static function installConfigFile(RequestAbstract $request): void
    {
        self::editConfigFile($request);
        self::editHtaccessFile($request);
    }

    protected static function editConfigFile(RequestAbstract $request): void
    {
        $db     = $request->getData('dbtype');
        $host   = $request->getData('dbhost');
        $port   = (int) $request->getData('dbport');
        $dbname = $request->getData('dbname');

        $admin  = ['login' => $request->getData('schemauser'), 'password' => $request->getData('schemapassword')];
        $insert = ['login' => $request->getData('createuser'), 'password' => $request->getData('createpassword')];
        $select = ['login' => $request->getData('selectuser'), 'password' => $request->getData('selectpassword')];
        $update = ['login' => $request->getData('updateuser'), 'password' => $request->getData('updatepassword')];
        $delete = ['login' => $request->getData('deleteuser'), 'password' => $request->getData('deletepassword')];
        $schema = ['login' => $request->getData('schemauser'), 'password' => $request->getData('schemapassword')];

        $subdir = $request->getData('websubdir');
        $tld    = $request->getData('domain');

        $pageType = $request->getData('installtype');

        $defaultApp = 'Frontend';
        if ($pageType === 'oem') {
            $defaultApp = 'Backend';
        }

        $defaultAppLower = \strtolower($defaultApp);

        $config = include __DIR__ . '/Templates/config.tpl.php';
        \file_put_contents(__DIR__ . '/../config.php', $config);

        $configJson = include __DIR__ . '/Templates/config.tpl.php';
        \file_put_contents(__DIR__ . '/../config.json', \json_decode($configJson, \JSON_PRETTY_PRINT));

        \unlink(__DIR__ . '/../config.php');
    }

    protected static function editHtaccessFile(RequestAbstract $request): void
    {
        $fullTLD = $request->getData('domain');
        $tld     = \str_replace(['.', 'http://', 'https://'], ['\.', '', ''], $request->getData('domain') ?? '');
        $subPath = $request->getData('websubdir') ?? '/';

        $config = include __DIR__ . '/Templates/htaccess.tpl.php';

        \file_put_contents(__DIR__ . '/../.htaccess', $config);
        \file_put_contents(__DIR__ . '/../../server/config.json', \json_encode($config, \JSON_PRETTY_PRINT));
    }

    protected static function installCore(ConnectionAbstract $db): void
    {
        self::createBaseTables($db);
        self::populateBaseTableData($db);
    }

    protected static function createBaseTables(ConnectionAbstract $db): void
    {
        $path = __DIR__ . '/db.json';
        if (!\is_file($path)) {
            return; // @codeCoverageIgnore
        }

        $content = \file_get_contents($path);
        if ($content === false) {
            return; // @codeCoverageIgnore
        }

        $definitions = \json_decode($content, true);
        foreach ($definitions as $definition) {
            SchemaBuilder::createFromSchema($definition, $db)->execute();
        }
    }

    protected static function populateBaseTableData(ConnectionAbstract $db): void
    {
        $sqlite = new SQLiteConnection([
            'db'       => 'sqlite',
            'database' => __DIR__ . '/../phpOMS/Localization/Defaults/localization.sqlite',
        ]);

        self::installCountries($sqlite, $db);
        self::installLanguages($sqlite, $db);
        self::installCurrencies($sqlite, $db);

        $sqlite->close();
    }

    private static function installCountries(SQLiteConnection $sqlite, ConnectionAbstract $con): void
    {
        $query = new Builder($con);
        $query->insert('country_name', 'country_code2', 'country_code3', 'country_numeric', 'country_region', 'country_developed')
            ->into('country');

        $querySqlite = new Builder($sqlite);
        $countries   = $querySqlite->select('*')->from('country')->execute();

        if ($countries === null) {
            return;
        }

        foreach ($countries as $country) {
            $query->values(
                $country['country_name'] === null ? null : \trim($country['country_name']),
                $country['country_code2'] === null ? null : \trim($country['country_code2']),
                $country['country_code3'] === null ? null : \trim($country['country_code3']),
                $country['country_numeric'],
                $country['country_region'],
                (int) $country['country_developed']
            );
        }

        $query->execute();
    }

    private static function installLanguages(SQLiteConnection $sqlite, ConnectionAbstract $con): void
    {
        $query = new Builder($con);
        $query->insert('language_name', 'language_native', 'language_639_1', 'language_639_2T', 'language_639_2B', 'language_639_3')
            ->into('language');

        $querySqlite = new Builder($sqlite);
        $languages   = $querySqlite->select('*')->from('language')->execute();

        if ($languages === null) {
            return;
        }

        foreach ($languages as $language) {
            $query->values(
                $language['language_name'] === null ? null : \trim($language['language_name']),
                $language['language_native'] === null ? null : \trim($language['language_native']),
                $language['language_639_1'] === null ? null : \trim($language['language_639_1']),
                $language['language_639_2T'] === null ? null : \trim($language['language_639_2T']),
                $language['language_639_2B'] === null ? null : \trim($language['language_639_2B']),
                $language['language_639_3'] === null ? null : \trim($language['language_639_3'])
            );
        }

        $query->execute();
    }

    private static function installCurrencies(SQLiteConnection $sqlite, ConnectionAbstract $con): void
    {
        $query = new Builder($con);
        $query->insert('currency_id', 'currency_name', 'currency_code', 'currency_number', 'currency_symbol', 'currency_subunits', 'currency_decimal', 'currency_countries')
            ->into('currency');

        $querySqlite = new Builder($sqlite);
        $currencies  = $querySqlite->select('*')->from('currency')->execute();

        if ($currencies === null) {
            return;
        }

        foreach ($currencies as $currency) {
            $query->values(
                $currency['currency_id'],
                $currency['currency_name'] === null ? null : \trim($currency['currency_name']),
                $currency['currency_code'] === null ? null : \trim($currency['currency_code']),
                $currency['currency_number'] === null ? null : \trim($currency['currency_number']),
                $currency['currency_symbol'] === null ? null : \trim($currency['currency_symbol']),
                $currency['currency_subunits'],
                $currency['currency_decimal'] === null ? null : \trim($currency['currency_decimal']),
                $currency['currency_countries'] === null ? null : \trim($currency['currency_countries'])
            );
        }

        $query->execute();
    }

    protected static function installGroups(ConnectionAbstract $db): void
    {
        self::installMainGroups($db);
    }

    protected static function installMainGroups(ConnectionAbstract $db): void
    {
        $guest = new Group('guest');
        $guest->setStatus(GroupStatus::ACTIVE);
        GroupMapper::create()->execute($guest);

        $user = new Group('user');
        $user->setStatus(GroupStatus::ACTIVE);
        GroupMapper::create()->execute($user);

        $admin = new Group('admin');
        $admin->setStatus(GroupStatus::ACTIVE);
        GroupMapper::create()->execute($admin);
    }

    protected static function installUsers(RequestAbstract $request, ConnectionAbstract $db): void
    {
        self::installMainUser($request, $db);
    }

    protected static function installApplications(RequestAbstract $request, ConnectionAbstract $db): void
    {
        if ($request->getData('installtype') === 'orm') {
            \copy(__DIR__ . '/Templates/ORMRoutes.php', __DIR__ . '/../Routes.php');
        } else {
            \copy(__DIR__ . '/Templates/DistRoutes.php', __DIR__ . '/../Routes.php');
        }
    }

    protected static function installMainUser(RequestAbstract $request, ConnectionAbstract $db): void
    {
        $account = new Account();
        $account->setStatus(AccountStatus::ACTIVE);
        $account->tries = 0;
        $account->setType(AccountType::USER);
        $account->login = (string) $request->getData('adminname');
        $account->name1 = (string) $request->getData('adminname');
        $account->generatePassword((string) $request->getData('adminpassword'));
        $account->setEmail((string) $request->getData('adminemail'));

        $l11n = $account->l11n;
        $l11n->loadFromLanguage($request->getData('defaultlang') ?? 'en', $request->getData('defaultcountry') ?? 'us');

        AccountCredentialMapper::create()->execute($account);

        $sth = $db->con->prepare(
            'INSERT INTO `account_group` (`account_group_group`, `account_group_account`) VALUES
                (3, ' . $account->getId() . ');'
        );

        if ($sth === false) {
            return; // @codeCoverageIgnore
        }

        $sth->execute();
    }
}
