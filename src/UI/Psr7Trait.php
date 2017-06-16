<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use Lookyman\Nette\OAuth2\Server\Psr7\ApplicationPsr7ResponseInterface;
use Lookyman\Nette\OAuth2\Server\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;

trait Psr7Trait
{

	protected function createServerRequest(): ServerRequestInterface
	{
		return ServerRequestFactory::fromGlobals();
	}

	protected function createResponse(): ApplicationPsr7ResponseInterface
	{
		return new Response();
	}

	protected function createStream(): StreamInterface
	{
		return new Stream('php://temp', 'r+');
	}
}
