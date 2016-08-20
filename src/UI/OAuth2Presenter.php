<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Psr7\Response;
use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class OAuth2Presenter extends Presenter implements LoggerAwareInterface
{
	use LoggerAwareTrait;
	use Psr7Trait;

	const SESSION_NAMESPACE = 'nette-oauth2-server';

	/**
	 * @var AuthorizationServer
	 * @inject
	 */
	public $authorizationServer;

	/**
	 * @var IAuthorizationRequestSerializer|null
	 */
	private $authorizationRequestSerializer;

	/**
	 * @var RedirectConfig|null
	 */
	private $redirectConfig;

	/**
	 * @throws AbortException
	 * @throws BadRequestException
	 */
	public function actionAccessToken()
	{
		if (!$this->getHttpRequest()->isMethod(IRequest::POST)) {
			$body = $this->createStream();
			$body->write('Method not allowed');
			$this->sendResponse((new Response())->withStatus(IResponse::S405_METHOD_NOT_ALLOWED)->withBody($body));
		}

		try {
			$request = $this->createServerRequest();
			$response = $this->createResponse();

			$this->sendResponse($this->authorizationServer->respondToAccessTokenRequest($request, $response));

		} catch (AbortException $e) {
			throw $e;

		} catch (OAuthServerException $e) {
			$this->sendResponse($e->generateHttpResponse($response));

		} catch (\Exception $e) {
			if ($this->logger) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
			}
			$body = $this->createStream();
			$body->write('Unknown error');
			$this->sendResponse($response->withStatus(IResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body));
		}
	}

	/**
	 * @throws AbortException
	 * @throws BadRequestException
	 */
	public function actionAuthorize()
	{
		if (!$this->getHttpRequest()->isMethod(IRequest::GET)) {
			$body = $this->createStream();
			$body->write('Method not allowed');
			$this->sendResponse((new Response())->withStatus(IResponse::S405_METHOD_NOT_ALLOWED)->withBody($body));
		}

		try {
			$request = $this->createServerRequest();
			$response = $this->createResponse();

			$this->saveAuthorizationRequest($this->authorizationServer->validateAuthorizationRequest($request));

			$this->redirectToLogin();
			$this->redirectToApprove();

		} catch (AbortException $e) {
			throw $e;

		} catch (OAuthServerException $e) {
			$this->sendResponse($e->generateHttpResponse($response));

		} catch (\Exception $e) {
			if ($this->logger) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
			}
			$body = $this->createStream();
			$body->write('Unknown error');
			$this->sendResponse($response->withStatus(IResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body));
		}
	}

	/**
	 * @param IAuthorizationRequestSerializer $authorizationRequestSerializer
	 */
	public function setAuthorizationRequestSerializer(IAuthorizationRequestSerializer $authorizationRequestSerializer)
	{
		$this->authorizationRequestSerializer = $authorizationRequestSerializer;
	}

	/**
	 * @param RedirectConfig $redirectConfig
	 */
	public function setRedirectConfig(RedirectConfig $redirectConfig)
	{
		$this->redirectConfig = $redirectConfig;
	}

	/**
	 * @throws AbortException
	 * @throws BadRequestException
	 */
	private function redirectToApprove()
	{
		if (!$this->redirectConfig) {
			$body = $this->createStream();
			$body->write('RedirectConfig not set');
			$this->sendResponse((new Response())->withStatus(IResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body));
		}
		call_user_func_array([$this, 'redirect'], $this->redirectConfig->getApproveDestination());
	}

	/**
	 * @throws AbortException
	 * @throws BadRequestException
	 */
	private function redirectToLogin()
	{
		if (!$this->redirectConfig) {
			$body = $this->createStream();
			$body->write('RedirectConfig not set');
			$this->sendResponse((new Response())->withStatus(IResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body));
		}
		if (!$this->getUser()->isLoggedIn()) {
			call_user_func_array([$this, 'redirect'], $this->redirectConfig->getLoginDestination());
		}
	}

	/**
	 * @param AuthorizationRequest $authorizationRequest
	 * @throws BadRequestException
	 */
	private function saveAuthorizationRequest(AuthorizationRequest $authorizationRequest)
	{
		if (!$this->authorizationRequestSerializer) {
			$body = $this->createStream();
			$body->write('AuthorizationRequestSerializer not set');
			$this->sendResponse((new Response())->withStatus(IResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body));
		}
		$this->getSession(self::SESSION_NAMESPACE)->authorizationRequest = $this->authorizationRequestSerializer->serialize($authorizationRequest);
	}
}
