<?php

declare(strict_types=1);

namespace Controllers;

use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;
use phpOMS\Utils\Parser\Markdown\Markdown;

class BackendController
{
	public function signinView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Backend/tpl/signin');

		return $view;
	}
}
