<?php

/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Applications\Api
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */

declare(strict_types=1);

namespace Applications\Api;

use Models\AccountMapper;
use Models\LocalizationMapper;
use Models\NullAccount;
use phpOMS\Account\Account;
use phpOMS\Account\AccountManager;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\Application\ApplicationStatus;
use phpOMS\Auth\Auth;
use phpOMS\DataStorage\Cache\CachePool;
use phpOMS\DataStorage\Cookie\CookieJar;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Localization\L11nManager;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Model\Html\Head;
use phpOMS\Module\ModuleManager;
use phpOMS\Router\RouteVerb;
use phpOMS\Router\WebRouter;
use phpOMS\System\File\PathException;
use phpOMS\System\MimeType;
use phpOMS\Uri\UriFactory;
use phpOMS\Views\View;
use WebApplication;

final class Application
{
    private WebApplication $app;

    private array $config;

    public function __construct(WebApplication $app, array $config)
    {
        $this->app          = $app;
        $this->app->appName = 'Api';
        $this->config       = $config;
        UriFactory::setQuery('/app', \strtolower($this->app->appName));
    }

    public function run(HttpRequest $request, HttpResponse $response): void
    {
        $response->header->set('Content-Type', 'text/plain; charset=utf-8');
        $pageView = new View($this->app->l11nManager, $request, $response);

        $this->app->l11nManager = new L11nManager($this->app->appName);
        $this->app->dbPool      = new DatabasePool();
        $this->app->router      = new WebRouter($this->app);
        $this->app->router->importFromFile(__DIR__ . '/Routes.php');

        $this->app->sessionManager = new HttpSession(0);
        $this->app->cookieJar      = new CookieJar();
        $this->app->dispatcher     = new Dispatcher($this->app);

        $this->app->dbPool->create('core', $this->config['db']['core']['masters']['admin']);
        $this->app->dbPool->create('insert', $this->config['db']['core']['masters']['insert']);
        $this->app->dbPool->create('select', $this->config['db']['core']['masters']['select']);
        $this->app->dbPool->create('update', $this->config['db']['core']['masters']['update']);
        $this->app->dbPool->create('delete', $this->config['db']['core']['masters']['delete']);
        $this->app->dbPool->create('schema', $this->config['db']['core']['masters']['schema']);

        /* Checking csrf token, if a csrf token is required at all has to be decided in the route or controller */
        if ($request->getData('CSRF') !== null
            && !\hash_equals($this->app->sessionManager->get('CSRF'), $request->getData('CSRF'))
        ) {
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        /** @var \phpOMS\DataStorage\Database\Connection\ConnectionAbstract $con */
        $con = $this->app->dbPool->get();
        DataMapperFactory::db($con);

        $this->app->cachePool    = new CachePool();
        $this->app->eventManager = new EventManager($this->app->dispatcher);
        $this->app->eventManager->importFromFile(__DIR__ . '/Hooks.php');

        $this->app->accountManager = new AccountManager($this->app->sessionManager);
        $this->app->l11nServer     = LocalizationMapper::get()->where('id', 1)->execute();

        $aid                       = Auth::authenticate($this->app->sessionManager);
        $request->header->account  = $aid;
        $response->header->account = $aid;

        $account = $this->loadAccount($request);

        if (!($account instanceof NullAccount)) {
            $response->header->l11n = $account->l11n;
        } elseif ($this->app->sessionManager->get('language') !== null) {
            $response->header->l11n
                ->loadFromLanguage(
                    $this->app->sessionManager->get('language'),
                    $this->app->sessionManager->get('country') ?? '*'
                );
        } elseif ($this->app->cookieJar->get('language') !== null) {
            $response->header->l11n
                ->loadFromLanguage(
                    $this->app->cookieJar->get('language'),
                    $this->app->cookieJar->get('country') ?? '*'
                );
        }

        UriFactory::setQuery('/lang', $response->getLanguage());
        $response->header->set('content-language', $response->getLanguage(), true);

        $appStatus = ApplicationStatus::NORMAL;
        if ($appStatus === ApplicationStatus::READ_ONLY ||  $appStatus === ApplicationStatus::DISABLED) {
            if (!$account->hasGroup(3)) {
                if ($request->getRouteVerb() !== RouteVerb::GET) {
                    // Application is in read only mode or completely disabled
                    // If read only mode is active only GET requests are allowed
                    // A user who is part of the admin group is excluded from this rule
                    $response->header->status = RequestStatusCode::R_405;

                    return;
                }

                $this->app->dbPool->remove('admin');
                $this->app->dbPool->remove('insert');
                $this->app->dbPool->remove('update');
                $this->app->dbPool->remove('delete');
                $this->app->dbPool->remove('schema');
            }
        }

        $routed = $this->app->router->route(
            $request->uri->getRoute(),
            $request->getData('CSRF'),
            $request->getRouteVerb(),
            $this->app->appName,
            $this->app->orgId,
            $account,
            $request->getData()
        );

        $dispatched = $this->app->dispatcher->dispatch($routed, $request, $response);

        if (empty($dispatched)) {
            $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
            $response->header->status = RequestStatusCode::R_404;
            $response->set($request->uri->__toString(), [
                'status'   => \phpOMS\Message\NotificationLevel::ERROR,
                'title'    => '',
                'message'  => '',
                'response' => [],
            ]);
        }

        $pageView->addData('dispatch', $dispatched);
    }

    private function loadAccount(HttpRequest $request): Account
    {
        /** @var Account $account */
        $account = AccountMapper::get()->with('groups')->with('l11n')->where('id', $request->header->account)->execute();
        $this->app->accountManager->add($account);

        return $account;
    }
}
