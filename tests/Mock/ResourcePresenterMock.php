<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\Mock;

use Lookyman\NetteOAuth2Server\UI\ResourcePresenter;
use Psr\Http\Message\ServerRequestInterface;

class ResourcePresenterMock extends ResourcePresenter
{

	/**
	 * @var ServerRequestInterface
	 */
	private $serverRequest;

	public function setServerRequest(ServerRequestInterface $serverRequest): void
	{
		$this->serverRequest = $serverRequest;
	}

	protected function createServerRequest(): ServerRequestInterface
	{
		return $this->serverRequest;
	}

	public function actionDefault(): void
	{
		$this->terminate();
	}

}
