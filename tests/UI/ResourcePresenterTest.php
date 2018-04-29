<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\UI;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Lookyman\NetteOAuth2Server\Psr7\ApplicationPsr7ResponseInterface;
use Lookyman\NetteOAuth2Server\Tests\Mock\ResourcePresenterMock;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\ServerRequest;

class ResourcePresenterTest extends TestCase
{

	/**
	 * @var IRequest|MockObject
	 */
	private $httpRequest;

	/**
	 * @var IResponse|MockObject
	 */
	private $httpResponse;

	/**
	 * @var ResourcePresenterMock
	 */
	private $presenter;

	/**
	 * @var ResourceServer|MockObject
	 */
	private $resourceServer;

	protected function setUp(): void
	{
		$this->httpRequest = $this->createMock(IRequest::class);
		$this->httpResponse = $this->createMock(IResponse::class);

		$this->resourceServer = $this->createMock(ResourceServer::class);

		$this->presenter = new ResourcePresenterMock();
		$this->presenter->injectPrimary(null, null, null, $this->httpRequest, $this->httpResponse);
		$this->presenter->resourceServer = $this->resourceServer;
	}

	public function testCheckRequirements(): void
	{
		$serverRequest = new ServerRequest();

		$this->resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willReturn($serverRequest);

		$this->presenter->setServerRequest($serverRequest);
		$this->presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest): void {
			self::assertSame($serverRequest, $request);
		};
		$this->presenter->run(new Request(''));
	}

	public function testOAuthException(): void
	{
		$serverRequest = new ServerRequest();

		$this->resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException(new OAuthServerException('message', 1, 'type', 2));

		$this->presenter->setServerRequest($serverRequest);
		$this->presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest): void {
			self::assertSame($serverRequest, $request);
		};
		$response = $this->presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->httpResponse->expects(self::once())->method('setCode')->with(2);
		$this->expectOutputString('{"error":"type","message":"message"}');
		$response->send($this->httpRequest, $this->httpResponse);
	}

	public function testExceptionWithoutLogger(): void
	{
		$serverRequest = new ServerRequest();

		$this->resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException(new \Exception());

		$this->presenter->setServerRequest($serverRequest);
		$this->presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest): void {
			self::assertSame($serverRequest, $request);
		};
		$response = $this->presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->httpResponse->expects(self::once())->method('setCode')->with(500);
		$this->expectOutputString('Unknown error');
		$response->send($this->httpRequest, $this->httpResponse);
	}

	public function testExceptionWithLogger(): void
	{
		$serverRequest = new ServerRequest();

		$exception = new \Exception('ex');

		$this->resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException($exception);

		$logger = $this->createMock(LoggerInterface::class);
		$logger->expects(self::once())->method('error')->with('ex', ['exception' => $exception]);

		$this->presenter->setServerRequest($serverRequest);
		$this->presenter->setLogger($logger);
		$this->presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest): void {
			self::assertSame($serverRequest, $request);
		};
		$response = $this->presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->httpResponse->expects(self::once())->method('setCode')->with(500);
		$this->expectOutputString('Unknown error');
		$response->send($this->httpRequest, $this->httpResponse);
	}

}
