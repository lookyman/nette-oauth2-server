<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\Mock;

use Lookyman\NetteOAuth2Server\UI\ResourcePresenter;
use Nette\Application\AbortException;
use Psr\Http\Message\ServerRequestInterface;

class ResourcePresenterMock extends ResourcePresenter
{
	/**
	 * @var ServerRequestInterface
	 */
	private $serverRequest;

	/**
	 * @param ServerRequestInterface $serverRequest
	 */
	public function setServerRequest(ServerRequestInterface $serverRequest)
	{
		$this->serverRequest = $serverRequest;
	}

	/**
	 * @return ServerRequestInterface
	 */
	protected function createServerRequest(): ServerRequestInterface
	{
		return $this->serverRequest;
	}

	/**
	 * @throws AbortException
	 */
	public function actionDefault()
	{
		$this->terminate();
	}
}
