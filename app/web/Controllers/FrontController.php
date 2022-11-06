<?php

declare(strict_types=1);

namespace Controllers;

use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;
use phpOMS\Utils\Parser\Markdown\Markdown;
use WebApplication;

class FrontController
{
	private WebApplication $app;

    public function __construct(WebApplication $app = null)
    {
        $this->app = $app;
	}

	public function frontView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Frontend/tpl/front');

		return $view;
	}

	public function featureView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Frontend/tpl/features');

		return $view;
	}

	public function pricingView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Frontend/tpl/pricing');

		return $view;
	}

	public function signupView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Frontend/tpl/signin');

		return $view;
	}

	public function imprintView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Frontend/tpl/default');

		$lang = $request->getLanguage();

		$path = \is_file(__DIR__ . '/../Applications/Frontend/content/imprint.' . $lang . '.md')
			? __DIR__ . '/../Applications/Frontend/content/imprint.' . $lang . '.md'
			: __DIR__ . '/../Applications/Frontend/content/imprint.en.md';

		$markdown = Markdown::parse(\file_get_contents($path));

		$view->setData('text', $markdown);

		return $view;
	}

	public function termsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Frontend/tpl/default');

		$lang = $request->getLanguage();

		$path = \is_file(__DIR__ . '/../Applications/Frontend/content/terms.' . $lang . '.md')
			? __DIR__ . '/../Applications/Frontend/content/terms.' . $lang . '.md'
			: __DIR__ . '/../Applications/Frontend/content/terms.en.md';

		$markdown = Markdown::parse(\file_get_contents($path));

		$view->setData('text', $markdown);

		return $view;
	}

	public function privacyView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Frontend/tpl/default');

		$lang = $request->getLanguage();

		$path = \is_file(__DIR__ . '/../Applications/Frontend/content/privacy.' . $lang . '.md')
			? __DIR__ . '/../Applications/Frontend/content/privacy.' . $lang . '.md'
			: __DIR__ . '/../Applications/Frontend/content/privacy.en.md';

		$markdown = Markdown::parse(\file_get_contents($path));

		$view->setData('text', $markdown);

		return $view;
	}

	public function contactView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Frontend/tpl/contact');

		return $view;
	}
}
