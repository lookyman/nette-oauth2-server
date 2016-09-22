<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Psr7\Response;
use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\Tests\Mock\OAuth2PresenterMock;
use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Application\Responses\RedirectResponse;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\IResponse as HttpResponse;
use Nette\Http\Session;
use Nette\Security\User;
use Psr\Log\LoggerInterface;

class OAuth2PresenterTest extends \PHPUnit_Framework_TestCase
{
	public function testActionAccessToken()
	{
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(true);

		$response = $this->getMockBuilder(IResponse::class)->disableOriginalConstructor()->getMock();

		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
		$authorizationServer->expects(self::once())->method('respondToAccessTokenRequest')->willReturn($response);

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock());
		$presenter->authorizationServer = $authorizationServer;

		self::assertSame($response, $presenter->run(new Request('', null, ['action' => 'accessToken'])));
	}

	public function testActionAccessTokenWrongMethod()
	{
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(false);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
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
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(true);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S400_BAD_REQUEST);

		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
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
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(true);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S500_INTERNAL_SERVER_ERROR);

		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
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
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::POST)->willReturn(true);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S500_INTERNAL_SERVER_ERROR);

		$exception = new \Exception('foo');
		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
		$authorizationServer->expects(self::once())->method('respondToAccessTokenRequest')->willThrowException($exception);

		$logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
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
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(true);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();

		$session = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
		$session->expects(self::once())->method('getSection')->with(OAuth2Presenter::SESSION_NAMESPACE)->willReturn(new \stdClass());

		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
		$user->expects(self::once())->method('isLoggedIn')->willReturn(false);

		$request = new AuthorizationRequest();

		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
		$authorizationServer->expects(self::once())->method('validateAuthorizationRequest')->willReturn($request);

		$config = new RedirectConfig('foo', 'bar');

		$presenter = new OAuth2PresenterMock(function ($destination) {
			self::assertEquals('bar', $destination);
		});
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $session, $user);
		$presenter->authorizationRequestSerializer = $this->getMockBuilder(IAuthorizationRequestSerializer::class)->disableOriginalConstructor()->getMock();
		$presenter->authorizationServer = $authorizationServer;
		$presenter->redirectConfig = $config;

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(RedirectResponse::class, $response);
	}

	public function testActionAuthorizeLogged()
	{
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(true);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();

		$session = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
		$session->expects(self::once())->method('getSection')->with(OAuth2Presenter::SESSION_NAMESPACE)->willReturn(new \stdClass());

		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
		$user->expects(self::once())->method('isLoggedIn')->willReturn(true);

		$request = new AuthorizationRequest();

		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
		$authorizationServer->expects(self::once())->method('validateAuthorizationRequest')->willReturn($request);

		$config = new RedirectConfig('foo', 'bar');

		$presenter = new OAuth2PresenterMock(function ($destination) {
			self::assertEquals('foo', $destination);
		});
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $session, $user);
		$presenter->authorizationRequestSerializer = $this->getMockBuilder(IAuthorizationRequestSerializer::class)->disableOriginalConstructor()->getMock();
		$presenter->authorizationServer = $authorizationServer;
		$presenter->redirectConfig = $config;

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(RedirectResponse::class, $response);
	}

	public function testActionAuthorizeWrongMethod()
	{
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(false);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S405_METHOD_NOT_ALLOWED);

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse);

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(Response::class, $response);
		$this->expectOutputString('Method not allowed');
		$response->send($httpRequest, $httpResponse);
	}

	public function testActionAuthorizeOAuthException()
	{
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(true);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S400_BAD_REQUEST);

		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
		$authorizationServer->expects(self::once())->method('validateAuthorizationRequest')->willThrowException(OAuthServerException::unsupportedGrantType());

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock());
		$presenter->authorizationRequestSerializer = $this->getMockBuilder(IAuthorizationRequestSerializer::class)->disableOriginalConstructor()->getMock();
		$presenter->authorizationServer = $authorizationServer;

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(Response::class, $response);
		ob_start();
		$response->send($httpRequest, $httpResponse);
		ob_end_clean();
	}

	public function testActionAuthorizeException()
	{
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(true);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S500_INTERNAL_SERVER_ERROR);

		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
		$authorizationServer->expects(self::once())->method('validateAuthorizationRequest')->willThrowException(new \Exception());

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock());
		$presenter->authorizationRequestSerializer = $this->getMockBuilder(IAuthorizationRequestSerializer::class)->disableOriginalConstructor()->getMock();
		$presenter->authorizationServer = $authorizationServer;

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(Response::class, $response);
		$this->expectOutputString('Unknown error');
		$response->send($httpRequest, $httpResponse);
	}

	public function testActionAuthorizeExceptionWithLogger()
	{
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpRequest->expects(self::once())->method('isMethod')->with(HttpRequest::GET)->willReturn(true);

		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();
		$httpResponse->expects(self::once())->method('setCode')->with(HttpResponse::S500_INTERNAL_SERVER_ERROR);

		$exception = new \Exception('foo');

		$authorizationServer = $this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock();
		$authorizationServer->expects(self::once())->method('validateAuthorizationRequest')->willThrowException($exception);

		$logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
		$logger->expects(self::once())->method('error')->with('foo', ['exception' => $exception]);

		$presenter = new OAuth2Presenter();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock());
		$presenter->authorizationRequestSerializer = $this->getMockBuilder(IAuthorizationRequestSerializer::class)->disableOriginalConstructor()->getMock();
		$presenter->authorizationServer = $authorizationServer;
		$presenter->setLogger($logger);

		$response = $presenter->run(new Request('', null, ['action' => 'authorize']));
		self::assertInstanceOf(Response::class, $response);
		$this->expectOutputString('Unknown error');
		$response->send($httpRequest, $httpResponse);
	}
}
