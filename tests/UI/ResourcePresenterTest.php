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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\ServerRequest;

class ResourcePresenterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var IRequest
	 */
	private $httpRequest;

	/**
	 * @var IResponse
	 */
	private $httpResponse;

	/**
	 * @var ResourcePresenterMock
	 */
	private $presenter;

	/**
	 * @var ResourceServer
	 */
	private $resourceServer;

	protected function setUp()
	{
		$this->httpRequest = $this->getMockBuilder(IRequest::class)->disableOriginalConstructor()->getMock();
		$this->httpResponse = $this->getMockBuilder(IResponse::class)->disableOriginalConstructor()->getMock();

		$this->resourceServer = $this->getMockBuilder(ResourceServer::class)->disableOriginalConstructor()->getMock();

		$this->presenter = new ResourcePresenterMock();
		$this->presenter->injectPrimary(null, null, null, $this->httpRequest, $this->httpResponse);
		$this->presenter->resourceServer = $this->resourceServer;
	}

	public function testCheckRequirements()
	{
		$serverRequest = new ServerRequest();

		$this->resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willReturn($serverRequest);

		$this->presenter->setServerRequest($serverRequest);
		$this->presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest) {
			self::assertSame($serverRequest, $request);
		};
		$this->presenter->run(new Request(''));
	}

	public function testOAuthException()
	{
		$serverRequest = new ServerRequest();

		$this->resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException(new OAuthServerException('message', 1, 'type', 2));

		$this->presenter->setServerRequest($serverRequest);
		$this->presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest) {
			self::assertSame($serverRequest, $request);
		};
		$response = $this->presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->httpResponse->expects(self::once())->method('setCode')->with(2);
		$this->expectOutputString('{"error":"type","message":"message"}');
		$response->send($this->httpRequest, $this->httpResponse);
	}

	public function testExceptionWithoutLogger()
	{
		$serverRequest = new ServerRequest();

		$this->resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException(new \Exception());

		$this->presenter->setServerRequest($serverRequest);
		$this->presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest) {
			self::assertSame($serverRequest, $request);
		};
		$response = $this->presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->httpResponse->expects(self::once())->method('setCode')->with(500);
		$this->expectOutputString('Unknown error');
		$response->send($this->httpRequest, $this->httpResponse);
	}

	public function testExceptionWithLogger()
	{
		$serverRequest = new ServerRequest();

		$exception = new \Exception('ex');

		$this->resourceServer->expects(self::once())->method('validateAuthenticatedRequest')->with($serverRequest)->willThrowException($exception);

		$logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
		$logger->expects(self::once())->method('error')->with('ex', ['exception' => $exception]);

		$this->presenter->setServerRequest($serverRequest);
		$this->presenter->setLogger($logger);
		$this->presenter->onAuthorized[] = function (ServerRequestInterface $request) use ($serverRequest) {
			self::assertSame($serverRequest, $request);
		};
		$response = $this->presenter->run(new Request(''));

		self::assertInstanceOf(ApplicationPsr7ResponseInterface::class, $response);
		$this->httpResponse->expects(self::once())->method('setCode')->with(500);
		$this->expectOutputString('Unknown error');
		$response->send($this->httpRequest, $this->httpResponse);
	}
}
