<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Applications\Frontend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Applications\Frontend;

use Models\CoreSettings;
use phpOMS\Account\Account;
use phpOMS\Account\NullAccount;
use phpOMS\Account\AccountManager;
use phpOMS\Asset\AssetType;
use phpOMS\Auth\Auth;
use phpOMS\DataStorage\Cache\CachePool;
use phpOMS\DataStorage\Cookie\CookieJar;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Localization\L11nManager;
use phpOMS\Localization\Localization;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestMethod;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Model\Html\Head;
use phpOMS\Router\RouteVerb;
use phpOMS\Router\WebRouter;
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
        $this->app->appName = 'Frontend';
        $this->config       = $config;
        UriFactory::setQuery('/app', \strtolower($this->app->appName));
    }

    public function run(HttpRequest $request, HttpResponse $response) : void
    {
        $this->app->l11nManager    = new L11nManager($this->app->appName);
        $this->app->dbPool         = new DatabasePool();
        $this->app->sessionManager = new HttpSession(0);
        $this->app->cookieJar      = new CookieJar();
        $this->app->dispatcher     = new Dispatcher($this->app);

        $this->app->router = new WebRouter();
        $this->app->router->importFromFile(__DIR__ . '/Routes.php');
        $this->app->router->add(
            '/backend/e403',
            function() use ($request, $response) {
                $view = new View($this->app->l11nManager, $request, $response);
                $view->setTemplate('/Web/Backend/Error/403_inline');
                $response->header->status = RequestStatusCode::R_403;

                return $view;
            },
            RouteVerb::GET
        );

        /* CSRF token OK? */
        if ($request->getData('CSRF') !== null
            && !\hash_equals($this->app->sessionManager->get('CSRF'), $request->getData('CSRF'))
        ) {
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        $this->app->cachePool      = new CachePool();
        $this->app->appSettings    = new CoreSettings();
        $this->app->eventManager   = new EventManager($this->app->dispatcher);
        $this->app->accountManager = new AccountManager($this->app->sessionManager);
        $this->app->l11nServer     = new Localization();

        $aid                       = Auth::authenticate($this->app->sessionManager);
        $request->header->account  = $aid;
        $response->header->account = $aid;

        $account = $this->loadAccount($request);

        if ($this->app->sessionManager->get('language') !== null) {
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

        if (!\in_array($response->getLanguage(), $this->config['language'])) {
            $response->header->l11n->setLanguage($this->app->l11nServer->getLanguage());
        }

        $pageView = new FrontendView($this->app->l11nManager, $request, $response);
        $head     = new Head();

        $pageView->setData('orgId', $this->app->orgId);
        $pageView->setData('head', $head);
        $response->set('Content', $pageView);

        /* Backend only allows GET */
        if ($request->getMethod() !== RequestMethod::GET) {
            $this->create406Response($response, $pageView);

            return;
        }

        UriFactory::setQuery('/lang', $response->getLanguage());

        $this->app->loadLanguageFromPath(
            $response->getLanguage(),
            __DIR__ . '/lang/' . $response->getLanguage() . '.lang.php'
        );

        $response->header->set('content-language', $response->getLanguage(), true);

        /* Create html head */
        $this->initResponseHead($head, $request, $response);

        $this->createDefaultPageView($request, $response, $pageView);

        $dispatched = $this->app->dispatcher->dispatch(
            $this->app->router->route(
                $request->uri->getRoute(),
                $request->getData('CSRF'),
                $request->getRouteVerb(),
                $this->app->appName,
                $this->app->orgId,
                $account,
                $request->getData()
            ),
            $request,
            $response
        );
        $pageView->addData('dispatch', $dispatched);
    }

    private function createDefaultPageView(HttpRequest $request, HttpResponse $response, FrontendView $pageView) : void
    {
        $pageView->setTemplate('/Applications/Frontend/index');
    }

    private function create403Response(HttpResponse $response, View $pageView) : void
    {
        $response->header->status = RequestStatusCode::R_403;
        $pageView->setTemplate('/Applications/Frontend/Error/403');
        $this->app->loadLanguageFromPath(
            $response->getLanguage(),
            __DIR__ . '/Error/lang/' . $response->getLanguage() . '.lang.php'
        );
    }

    private function create406Response(HttpResponse $response, View $pageView) : void
    {
        $response->header->status = RequestStatusCode::R_406;
        $pageView->setTemplate('/Applications/Frontend/Error/406');
        $this->app->loadLanguageFromPath(
            $response->getLanguage(),
            __DIR__ . '/Error/lang/' . $response->getLanguage() . '.lang.php'
        );
    }

    private function create503Response(HttpResponse $response, View $pageView) : void
    {
        $response->header->status = RequestStatusCode::R_503;
        $pageView->setTemplate('/Applications/Frontend/Error/503');
        $this->app->loadLanguageFromPath(
            $response->getLanguage(),
            __DIR__ . '/Error/lang/' . $response->getLanguage() . '.lang.php'
        );
    }

    private function loadAccount(HttpRequest $request) : Account
    {
        $account = new NullAccount();
        $this->app->accountManager->add($account);

        return $account;
    }

    private function initResponseHead(Head $head, HttpRequest $request, HttpResponse $response) : void
    {
        /* Load assets */
        $head->addAsset(AssetType::CSS, 'Resources/fonts/fontawesome/css/font-awesome.min.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/linearicons/css/style.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/lineicons/css/lineicons.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'cssOMS/styles.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/Roboto/roboto.css?v=1.0.0');

        // Framework
        $head->addAsset(AssetType::JS, 'jsOMS/Utils/oLib.js?v=1.0.0');
        $head->addAsset(AssetType::JS, 'jsOMS/UnhandledException.js?v=1.0.0');
        $head->addAsset(AssetType::JS, 'Applications/Frontend/js/frontend.js?v=1.0.0', ['type' => 'module']);

        $script = '';
        $response->header->set(
            'content-security-policy',
            'base-uri \'self\'; script-src \'self\' blob: \'sha256-'
            . \base64_encode(\hash('sha256', $script, true))
            . '\'; worker-src \'self\'',
            true
        );

        if ($request->hasData('debug')) {
            $head->addAsset(AssetType::CSS, 'cssOMS/debug.css?v=1.0.0');
            \phpOMS\DataStorage\Database\Query\Builder::$log = true;
        }

        $css = \file_get_contents(__DIR__ . '/css/small.css');
        if ($css === false) {
            $css = '';
        }

        $css = \preg_replace('!\s+!', ' ', $css);
        $head->setStyle('core', $css ?? '');
        $head->title = 'Karaka Frontend';
    }
}
