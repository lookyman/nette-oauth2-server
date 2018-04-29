<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\UI;

use Lookyman\NetteOAuth2Server\Psr7\ApplicationPsr7ResponseInterface;
use Lookyman\NetteOAuth2Server\Tests\Mock\Psr7TraitMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class Psr7TraitTest extends TestCase
{

	public function testCreateServerRequest(): void
	{
		$class = new Psr7TraitMock();
		$class->test();
		self::assertInstanceOf(ServerRequestInterface::class, $class->serverRequest);
		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $class->response);
		self::assertInstanceOf(StreamInterface::class, $class->stream);
	}

}
