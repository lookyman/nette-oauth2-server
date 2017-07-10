<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use Lookyman\Nette\OAuth2\Server\Mock\Psr7TraitMock;
use Lookyman\Nette\OAuth2\Server\Psr7\ApplicationPsr7ResponseInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \Lookyman\Nette\OAuth2\Server\UI\Psr7Trait
 */
final class Psr7TraitTest extends TestCase
{

	public function testCreateServerRequest()
	{
		$class = new Psr7TraitMock();
		$class->test();
		self::assertInstanceOf(ServerRequestInterface::class, $class->serverRequest);
		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $class->response);
		self::assertInstanceOf(StreamInterface::class, $class->stream);
	}

}
