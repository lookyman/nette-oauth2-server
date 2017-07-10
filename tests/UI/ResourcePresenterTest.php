<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Lookyman\Nette\OAuth2\Server\Mock\ResourcePresenterMock;
use Lookyman\Nette\OAuth2\Server\Psr7\ApplicationPsr7ResponseInterface;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\ServerRequest;

/**
 * @covers \Lookyman\Nette\OAuth2\Server\UI\ResourcePresenter
 */
final class ResourcePresenterTest extends TestCase
{

	public function testCheckRequirements()
	{
		$serverRequest = new ServerRequest();

		$resourceServer = $this->createMock(ResourceServer::class);
		$resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willReturn($serverRequest);

		$presenter = new ResourcePresenterMock();
		$presenter->injectPrimary(null, null, null, $this->createMock(IRequest::class), $this->createMock(IResponse::class));
		$presenter->resourceServer = $resourceServer;
		$presenter->setServerRequest($serverRequest);
		$presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest) {
			self::assertSame($serverRequest, $request);
		};
		$presenter->run(new Request(''));
	}

	public function testOAuthException()
	{
		$serverRequest = new ServerRequest();

		$resourceServer = $this->createMock(ResourceServer::class);
		$resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException(new OAuthServerException('message', 1, 'type', 2));

		$httpRequest = $this->createMock(IRequest::class);
		$httpResponse = $this->createMock(IResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(2);

		$presenter = new ResourcePresenterMock();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);
		$presenter->resourceServer = $resourceServer;
		$presenter->setServerRequest($serverRequest);
		$presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest) {
			self::assertSame($serverRequest, $request);
		};
		$response = $presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->expectOutputString('{"error":"type","message":"message"}');
		$response->send($httpRequest, $httpResponse);
	}

	public function testExceptionWithoutLogger()
	{
		$serverRequest = new ServerRequest();

		$resourceServer = $this->createMock(ResourceServer::class);
		$resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException(new \Exception());

		$httpRequest = $this->createMock(IRequest::class);
		$httpResponse = $this->createMock(IResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(500);

		$presenter = new ResourcePresenterMock();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);
		$presenter->resourceServer = $resourceServer;
		$presenter->setServerRequest($serverRequest);
		$presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest) {
			self::assertSame($serverRequest, $request);
		};
		$response = $presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->expectOutputString('Unknown error');
		$response->send($httpRequest, $httpResponse);
	}

	public function testExceptionWithLogger()
	{
		$serverRequest = new ServerRequest();

		$exception = new \Exception('ex');

		$resourceServer = $this->createMock(ResourceServer::class);
		$resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException($exception);

		$logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
		$logger->expects(self::once())->method('error')->with('ex', ['exception' => $exception]);

		$httpRequest = $this->createMock(IRequest::class);
		$httpResponse = $this->createMock(IResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(500);

		$presenter = new ResourcePresenterMock();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);
		$presenter->resourceServer = $resourceServer;
		$presenter->setServerRequest($serverRequest);
		$presenter->setLogger($logger);
		$presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest) {
			self::assertSame($serverRequest, $request);
		};
		$response = $presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->expectOutputString('Unknown error');
		$response->send($httpRequest, $httpResponse);
	}

}
