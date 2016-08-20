<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

use Lookyman\NetteOAuth2Server\Psr7\Response;
use Lookyman\NetteOAuth2Server\Psr7\ApplicationPsr7ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;

trait Psr7Trait
{
	/**
	 * @return ServerRequestInterface
	 * @throws \InvalidArgumentException
	 */
	protected function createServerRequest(): ServerRequestInterface
	{
		return ServerRequestFactory::fromGlobals();
	}

	/**
	 * @return ApplicationPsr7ResponseInterface
	 */
	protected function createResponse(): ApplicationPsr7ResponseInterface
	{
		return new Response();
	}

	/**
	 * @return StreamInterface
	 */
	protected function createStream(): StreamInterface
	{
		return new Stream('php://temp', 'r+');
	}
}
