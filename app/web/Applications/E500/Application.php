<?php
declare(strict_types=1);

namespace Applications\E500;

use phpOMS\Asset\AssetType;
use phpOMS\Localization\L11nManager;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Model\Html\Head;
use phpOMS\System\File\PathException;
use phpOMS\Views\View;
use WebApplication;

final class Application
{
    private WebApplication $app;

    private array $config = [];

    public function __construct(WebApplication $app, array $config)
    {
        $this->app          = $app;
        $this->config       = $config;
        $this->app->appName = 'E500';
    }

    public function run(HttpRequest $request, HttpResponse $response) : void
    {
        $pageView = new View($this->app->l11nManager, $request, $response);
        $pageView->setTemplate('/Applications/E500/index');
        $response->set('Content', $pageView);
        $response->header->status = RequestStatusCode::R_500;

        /* Load theme language */
        if (($path = \realpath($oldPath = __DIR__ . '/lang/' . $response->getLanguage() . '.lang.php')) === false) {
            throw new PathException($oldPath);
        }

        $this->app->l11nManager = new L11nManager($this->app->appName);

        /** @noinspection PhpIncludeInspection */
        $themeLanguage = include $path;
        $this->app->l11nManager->loadLanguage($response->getLanguage(), '0', $themeLanguage);

        $head    = new Head();
        $baseUri = $request->uri->getBase();
        $head->addAsset(AssetType::CSS, $baseUri . 'cssOMS/styles.css?v=1.0.0');

        $pageView->setData('head', $head);
    }
}
