<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\Psr7;

use Lookyman\NetteOAuth2Server\Psr7\Response;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

class ResponseTest extends TestCase
{

	public function testSend(): void
	{
		$httpRequest = $this->createMock(IRequest::class);
		$httpResponse = $this->createMock(IResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(500);
		$httpResponse->expects(self::once())->method('setHeader')->with('header', 'value');
		$body = new Stream('php://temp', 'r+');
		$body->write('test');
		$response = (new Response())->withStatus(500)->withHeader('header', 'value')->withBody($body);
		$this->expectOutputString('test');
		$response->send($httpRequest, $httpResponse);
	}

	public function testWithHeader(): void
	{
		$response = new Response();
		$modified = $response->withHeader('header', 'value');
		self::assertInstanceOf(Response::class, $modified);
		self::assertNotSame($response, $modified);
		$ref = new \ReflectionProperty($modified, 'headers');
		$ref->setAccessible(true);
		self::assertArrayHasKey('header', $ref->getValue($modified));
		self::assertEquals('value', $ref->getValue($modified)['header']);
	}

	public function testGetBody(): void
	{
		$response = new Response();
		$body = $response->getBody();
		self::assertInstanceOf(StreamInterface::class, $body);
		self::assertSame($body, $response->getBody());
	}

	public function testWithStatus(): void
	{
		$response = new Response();
		$modified = $response->withStatus(500);
		self::assertInstanceOf(Response::class, $modified);
		self::assertNotSame($response, $modified);
		$ref = new \ReflectionProperty($modified, 'code');
		$ref->setAccessible(true);
		self::assertEquals(500, $ref->getValue($modified));
	}

	public function testWithBody(): void
	{
		$body = $this->createMock(StreamInterface::class);
		$response = new Response();
		$modified = $response->withBody($body);
		self::assertInstanceOf(Response::class, $modified);
		self::assertNotSame($response, $modified);
		$ref = new \ReflectionProperty($modified, 'stream');
		$ref->setAccessible(true);
		self::assertSame($body, $ref->getValue($modified));
	}

	/**
	 * @param array $args
	 * @dataProvider notImplementedProvider
	 */
	public function testNotImplemented(string $method, array $args): void
	{
	    $this->expectException("\Nette\NotImplementedException");
		$response = new Response();
		call_user_func_array([$response, $method], $args);
	}

	/**
	 * @return array
	 */
	public function notImplementedProvider(): array
	{
		return [
			['getProtocolVersion', []],
			['withProtocolVersion', ['']],
			['getHeaders', []],
			['hasHeader', ['']],
			['getHeader', ['']],
			['getHeaderLine', ['']],
			['withAddedHeader', ['', '']],
			['withoutHeader', ['']],
			['getStatusCode', []],
			['getReasonPhrase', []],
		];
	}

}
