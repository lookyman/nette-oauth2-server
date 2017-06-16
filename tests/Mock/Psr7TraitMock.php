<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\Mock;

use Lookyman\Nette\OAuth2\Server\Psr7\ApplicationPsr7ResponseInterface;
use Lookyman\Nette\OAuth2\Server\UI\Psr7Trait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

final class Psr7TraitMock
{
	use Psr7Trait;

	/**
	 * @var ServerRequestInterface
	 */
	public $serverRequest;

	/**
	 * @var ApplicationPsr7ResponseInterface
	 */
	public $response;

	/**
	 * @var StreamInterface
	 */
	public $stream;

	public function test()
	{
		$this->serverRequest = $this->createServerRequest();
		$this->response = $this->createResponse();
		$this->stream = $this->createStream();
	}
}
