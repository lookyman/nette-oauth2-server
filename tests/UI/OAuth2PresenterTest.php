<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Lookyman\Nette\OAuth2\Server\Psr7\Response;
use Lookyman\Nette\OAuth2\Server\Storage\IAuthorizationRequestSerializer;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\IResponse as HttpResponse;
use Nette\Http\Session;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Lookyman\Nette\OAuth2\Server\UI\OAuth2Presenter
 */
final class OAuth2PresenterTest extends TestCase
{

	public function testActionAccessToken()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(true);

		$response = $this->createMock(IResponse::class);

		$authorizationServer = $this->createMock(AuthorizationServer::class);
		$authorizationServer->expects(self::once())->method('respondToAccessTokenRequest')->willReturn($response);

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $this->createMock(HttpResponse::class));
		$presenter->authorizationServer = $authorizationServer;

		self::assertSame($response, $presenter->run(new Request('', null, ['action' => 'accessToken'])));
	}

	public function testActionAccessTokenWrongMethod()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(false);

		$httpResponse = $this->createMock(HttpResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S405_METHOD_NOT_ALLOWED);

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);

		$response = $presenter->run(new Request('', null, ['action' => 'accessToken']));
		self::assertInstanceOf(Response::class, $response);
		$this->expectOutputString('Method not allowed');
		$response->send($httpRequest, $httpResponse);
	}

	public function testActionAccessTokenOAuthException()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(true);

		$httpResponse = $this->createMock(HttpResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S400_BAD_REQUEST);

		$authorizationServer = $this->createMock(AuthorizationServer::class);
		$authorizationServer->expects(self::once())->method('respondToAccessTokenRequest')->willThrowException(OAuthServerException::unsupportedGrantType());

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);
		$presenter->authorizationServer = $authorizationServer;

		$response = $presenter->run(new Request('', null, ['action' => 'accessToken']));
		self::assertInstanceOf(Response::class, $response);
		ob_start();
		$response->send($httpRequest, $httpResponse);
		ob_end_clean();
	}

	public function testActionAccessTokenException()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(true);

		$httpResponse = $this->createMock(HttpResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S500_INTERNAL_SERVER_ERROR);

		$authorizationServer = $this->createMock(AuthorizationServer::class);
		$authorizationServer->expects(self::once())->method('respondToAccessTokenRequest')->willThrowException(new \Exception());

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);
		$presenter->authorizationServer = $authorizationServer;

		$response = $presenter->run(new Request('', null, ['action' => 'accessToken']));
		self::assertInstanceOf(Response::class, $response);
		$this->expectOutputString('Unknown error');
		$response->send($httpRequest, $httpResponse);
	}

	public function testActionAccessTokenExceptionWithLogger()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(true);

		$httpResponse = $this->createMock(HttpResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S500_INTERNAL_SERVER_ERROR);

		$exception = new \Exception('foo');
		$authorizationServer = $this->createMock(AuthorizationServer::class);
		$authorizationServer->expects(self::once())->method('respondToAccessTokenRequest')->willThrowException($exception);

		$logger = $this->createMock(LoggerInterface::class);
		$logger->expects(self::once())->method('error')->with('foo', ['exception' => $exception]);

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);
		$presenter->authorizationServer = $authorizationServer;
		$presenter->setLogger($logger);

		$response = $presenter->run(new Request('', null, ['action' => 'accessToken']));
		self::assertInstanceOf(Response::class, $response);
		$this->expectOutputString('Unknown error');
		$response->send($httpRequest, $httpResponse);
	}

	public function testActionAuthorizeUnlogged()
	{
		self::markTestIncomplete('todo');
	}

	public function testActionAuthorizeLogged()
	{
		self::markTestIncomplete('todo');
	}

	public function testActionAuthorizeWrongMethod()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(false);

		$httpResponse = $this->createMock(HttpResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S405_METHOD_NOT_ALLOWED);

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);

		$redirectEventTriggered = false;
		$presenter->onBeforeRedirect[] = function () use (&$redirectEventTriggered) {
			$redirectEventTriggered = true;
		};

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(Response::class, $response);
		self::assertFalse($redirectEventTriggered);
		$this->expectOutputString('Method not allowed');
		$response->send($httpRequest, $httpResponse);
	}

	public function testActionAuthorizeOAuthException()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(true);

		$httpResponse = $this->createMock(HttpResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S400_BAD_REQUEST);

		$authorizationServer = $this->createMock(AuthorizationServer::class);
		$authorizationServer->expects(self::once())->method('validateAuthorizationRequest')->willThrowException(OAuthServerException::unsupportedGrantType());

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $this->createMock(Session::class));
		$presenter->authorizationRequestSerializer = $this->createMock(IAuthorizationRequestSerializer::class);
		$presenter->authorizationServer = $authorizationServer;

		$redirectEventTriggered = false;
		$presenter->onBeforeRedirect[] = function () use (&$redirectEventTriggered) {
			$redirectEventTriggered = true;
		};

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(Response::class, $response);
		self::assertFalse($redirectEventTriggered);
		ob_start();
		$response->send($httpRequest, $httpResponse);
		ob_end_clean();
	}

	public function testActionAuthorizeException()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(true);

		$httpResponse = $this->createMock(HttpResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S500_INTERNAL_SERVER_ERROR);

		$authorizationServer = $this->createMock(AuthorizationServer::class);
		$authorizationServer->expects(self::once())->method('validateAuthorizationRequest')->willThrowException(new \Exception());

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $this->createMock(Session::class));
		$presenter->authorizationRequestSerializer = $this->createMock(IAuthorizationRequestSerializer::class);
		$presenter->authorizationServer = $authorizationServer;

		$redirectEventTriggered = false;
		$presenter->onBeforeRedirect[] = function () use (&$redirectEventTriggered) {
			$redirectEventTriggered = true;
		};

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(Response::class, $response);
		self::assertFalse($redirectEventTriggered);
		$this->expectOutputString('Unknown error');
		$response->send($httpRequest, $httpResponse);
	}

	public function testActionAuthorizeExceptionWithLogger()
	{
		$httpRequest = $this->createMock(HttpRequest::class);
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(true);

		$httpResponse = $this->createMock(HttpResponse::class);
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S500_INTERNAL_SERVER_ERROR);

		$exception = new \Exception('foo');

		$authorizationServer = $this->createMock(AuthorizationServer::class);
		$authorizationServer->expects(self::once())->method('validateAuthorizationRequest')->willThrowException($exception);

		$logger = $this->createMock(LoggerInterface::class);
		$logger->expects(self::once())->method('error')->with('foo', ['exception' => $exception]);

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $this->createMock(Session::class));
		$presenter->authorizationRequestSerializer = $this->createMock(IAuthorizationRequestSerializer::class);
		$presenter->authorizationServer = $authorizationServer;
		$presenter->setLogger($logger);

		$redirectEventTriggered = false;
		$presenter->onBeforeRedirect[] = function () use (&$redirectEventTriggered) {
			$redirectEventTriggered = true;
		};

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(Response::class, $response);
		self::assertFalse($redirectEventTriggered);
		$this->expectOutputString('Unknown error');
		$response->send($httpRequest, $httpResponse);
	}
}
