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
use phpOMS\DataStorage\Database\Schema\Builder as SchemaBuilder;
use phpOMS\Message\RequestAbstract;

abstract class InstallAbstract extends ApplicationAbstract
{
    protected function setupHandlers() : void
    {
        \set_exception_handler(['\phpOMS\UnhandledHandler', 'exceptionHandler']);
        \set_error_handler(['\phpOMS\UnhandledHandler', 'errorHandler']);
        \register_shutdown_function(['\phpOMS\UnhandledHandler', 'shutdownHandler']);
        \mb_internal_encoding('UTF-8');
    }

    protected static function clearOld() : void
    {
        \file_put_contents(__DIR__ . '/../Routes.php', '<?php return [];');
        \file_put_contents(__DIR__ . '/../Hooks.php', '<?php return [];');
    }

    protected static function hasPhpExtensions() : bool
    {
        return \extension_loaded('pdo')
            && \extension_loaded('mbstring');
    }

    protected static function testDbConnection(RequestAbstract $request) : bool
    {
        return true;
    }

    protected static function setupDatabaseConnection(RequestAbstract $request) : ConnectionAbstract
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

    protected static function installConfigFile(RequestAbstract $request) : void
    {
        self::editConfigFile($request);
        self::editHtaccessFile($request);
    }

    protected static function editConfigFile(RequestAbstract $request) : void
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
    }

    protected static function editHtaccessFile(RequestAbstract $request) : void
    {
        $fullTLD = $request->getData('domain');
        $tld     = \str_replace(['.', 'http://', 'https://'], ['\.', '', ''], $request->getData('domain') ?? '');
        $subPath = $request->getData('websubdir') ?? '/';

        $config = include __DIR__ . '/Templates/htaccess.tpl.php';

        \file_put_contents(__DIR__ . '/../.htaccess', $config);
        \file_put_contents(__DIR__ . '/../../server/config.json', \json_encode($config, \JSON_PRETTY_PRINT));
    }

    protected static function installCore(ConnectionAbstract $db) : void
    {
        self::createBaseTables($db);
    }

    protected static function createBaseTables(ConnectionAbstract $db) : void
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

    protected static function installGroups(ConnectionAbstract $db) : void
    {
        self::installMainGroups($db);
        self::installGroupPermissions($db);
    }

    protected static function installMainGroups(ConnectionAbstract $db) : void
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

    protected static function installGroupPermissions(ConnectionAbstract $db) : void
    {
        $searchPermission = new GroupPermission(
            group: 2,
            category: PermissionCategory::SEARCH,
            permission: PermissionType::READ
        );

        $adminPermission = new GroupPermission(
            group: 3,
            permission: PermissionType::READ | PermissionType::CREATE | PermissionType::MODIFY | PermissionType::DELETE | PermissionType::PERMISSION
        );

        GroupPermissionMapper::create()->execute($searchPermission);
        GroupPermissionMapper::create()->execute($adminPermission);
    }

    protected static function installUsers(RequestAbstract $request, ConnectionAbstract $db) : void
    {
        self::installMainUser($request, $db);
    }

    protected static function installApplications(RequestAbstract $request, ConnectionAbstract $db) : void
    {
        if ($request->getData('installtype') === 'orm') {
            \copy(__DIR__ . '/Templates/ORMRoutes.php', __DIR__ . '/../Routes.php');
        } else {
            \copy(__DIR__ . '/Templates/DistRoutes.php', __DIR__ . '/../Routes.php');
        }
    }

    protected static function installMainUser(RequestAbstract $request, ConnectionAbstract $db) : void
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
