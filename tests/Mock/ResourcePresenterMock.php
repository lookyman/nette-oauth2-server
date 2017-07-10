<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\Mock;

use Lookyman\Nette\OAuth2\Server\UI\ResourcePresenter;
use Psr\Http\Message\ServerRequestInterface;

final class ResourcePresenterMock extends ResourcePresenter
{

	/**
	 * @var ServerRequestInterface
	 */
	private $serverRequest;

	public function setServerRequest(ServerRequestInterface $serverRequest)
	{
		$this->serverRequest = $serverRequest;
	}

	protected function createServerRequest(): ServerRequestInterface
	{
		return $this->serverRequest;
	}

	public function actionDefault()
	{
		$this->terminate();
	}

}
