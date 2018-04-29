<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\Mock;

use Lookyman\NetteOAuth2Server\Psr7\ApplicationPsr7ResponseInterface;
use Lookyman\NetteOAuth2Server\UI\Psr7Trait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class Psr7TraitMock
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

	public function test(): void
	{
		$this->serverRequest = $this->createServerRequest();
		$this->response = $this->createResponse();
		$this->stream = $this->createStream();
	}

}
