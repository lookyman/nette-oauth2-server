<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Lookyman\Nette\OAuth2\Server\Psr7\ApplicationPsr7ResponseInterface;
use Lookyman\Nette\OAuth2\Server\RedirectConfig;
use Lookyman\Nette\OAuth2\Server\Storage\IAuthorizationRequestSerializer;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class OAuth2Presenter extends Presenter implements LoggerAwareInterface
{
	use LoggerAwareTrait;
	use Psr7Trait;

	const SESSION_NAMESPACE = 'nette-oauth2-server';

	/**
	 * @var IAuthorizationRequestSerializer
	 * @inject
	 */
	public $authorizationRequestSerializer;

	/**
	 * @var AuthorizationServer
	 * @inject
	 */
	public $authorizationServer;

	/**
	 * @var RedirectConfig
	 * @inject
	 */
	public $redirectConfig;

	public function actionAccessToken()
	{
		$response = $this->createResponse();

		if (!$this->getHttpRequest()->isMethod(IRequest::POST)) {
			$body = $this->createStream();
			$body->write('Method not allowed');
			/** @var ApplicationPsr7ResponseInterface $response */
			$response = $response->withStatus(IResponse::S405_METHOD_NOT_ALLOWED)->withBody($body);
			$this->sendResponse($response);
		}

		try {
			/** @var ApplicationPsr7ResponseInterface $response */
			$response = $this->authorizationServer->respondToAccessTokenRequest($this->createServerRequest(), $response);
			$this->sendResponse($response);

		} catch (AbortException $e) {
			throw $e;

		} catch (OAuthServerException $e) {
			/** @var ApplicationPsr7ResponseInterface $response */
			$response = $e->generateHttpResponse($response);
			$this->sendResponse($response);

		} catch (\Throwable $e) {
			if ($this->logger) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
			}
			$body = $this->createStream();
			$body->write('Unknown error');
			/** @var ApplicationPsr7ResponseInterface $response */
			$response = $response->withStatus(IResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body);
			$this->sendResponse($response);
		}
	}

	public function actionAuthorize()
	{
		$response = $this->createResponse();

		if (!$this->getHttpRequest()->isMethod(IRequest::GET)) {
			$body = $this->createStream();
			$body->write('Method not allowed');
			/** @var ApplicationPsr7ResponseInterface $response */
			$response = $response->withStatus(IResponse::S405_METHOD_NOT_ALLOWED)->withBody($body);
			$this->sendResponse($response);
		}

		try {
			$this->getSession(self::SESSION_NAMESPACE)->authorizationRequest = $this->authorizationRequestSerializer->serialize(
				$this->authorizationServer->validateAuthorizationRequest($this->createServerRequest())
			);
			if (!$this->getUser()->isLoggedIn()) {
				$this->redirectConfig->redirectToLoginDestination($this);
			}
			$this->redirectConfig->redirectToApproveDestination($this);

		} catch (AbortException $e) {
			throw $e;

		} catch (OAuthServerException $e) {
			/** @var ApplicationPsr7ResponseInterface $response */
			$response = $e->generateHttpResponse($response);
			$this->sendResponse($response);

		} catch (\Throwable $e) {
			if ($this->logger) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
			}
			$body = $this->createStream();
			$body->write('Unknown error');
			/** @var ApplicationPsr7ResponseInterface $response */
			$response = $response->withStatus(IResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body);
			$this->sendResponse($response);
		}
	}

}
