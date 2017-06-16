<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Lookyman\Nette\OAuth2\Server\Psr7\ApplicationPsr7ResponseInterface;
use Nette\Application\UI\ComponentReflection;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * @method void onAuthorized(ServerRequestInterface $request)
 */
abstract class ResourcePresenter extends Presenter implements LoggerAwareInterface
{
	use LoggerAwareTrait;
	use Psr7Trait;

	/**
	 * @var callable[]
	 */
	public $onAuthorized;

	/**
	 * @var ResourceServer
	 * @inject
	 */
	public $resourceServer;

	/**
	 * @param mixed $element
	 */
	final public function checkRequirements($element)
	{
		if (!$element instanceof ComponentReflection) {
			return;
		}

		$request = $this->createServerRequest();
		$response = $this->createResponse();
		try {
			$request = $this->resourceServer->validateAuthenticatedRequest($request);

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
			$this->sendResponse($response->withStatus(IResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body));
		}

		$this->onAuthorized($request);
	}
}
