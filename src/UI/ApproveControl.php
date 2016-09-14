<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Nette\Application\AbortException;
use Nette\Application\IResponse;
use Nette\Application\UI\Control;
use Nette\Http\IResponse as HttpResponse;
use Nextras\Application\UI\SecuredLinksControlTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * @method void onAuthorizationComplete()
 * @method void onResponse(IResponse $response)
 */
class ApproveControl extends Control implements LoggerAwareInterface
{
	use LoggerAwareTrait;
	use Psr7Trait;
	use SecuredLinksControlTrait;

	/**
	 * @var callable[]
	 */
	public $onAuthorizationComplete;

	/**
	 * @var callable[]
	 */
	public $onResponse;

	/**
	 * @var AuthorizationRequest|null
	 */
	private $authorizationRequest;

	/**
	 * @var AuthorizationServer
	 */
	private $authorizationServer;

	/**
	 * @var string
	 */
	private $templateFile;

	/**
	 * @param AuthorizationServer $authorizationServer
	 */
	public function __construct(AuthorizationServer $authorizationServer)
	{
		$this->authorizationServer = $authorizationServer;
		$this->templateFile = __DIR__ . '/templates/approve.latte';
	}

	public function render()
	{
		$this->template->setFile($this->templateFile);
		$this->template->authorizationRequest = $this->authorizationRequest;
		$this->template->render();
	}

	/**
	 * @secured
	 */
	public function handleApprove()
	{
		$this->authorizationRequest->setAuthorizationApproved(true);
		$this->completeAuthorizationRequest();
	}

	/**
	 * @secured
	 */
	public function handleDeny()
	{
		$this->authorizationRequest->setAuthorizationApproved(false);
		$this->completeAuthorizationRequest();
	}

	public function completeAuthorizationRequest()
	{
		$this->onAuthorizationComplete();

		$response = $this->createResponse();
		try {
			$this->onResponse($this->authorizationServer->completeAuthorizationRequest($this->authorizationRequest, $response));

		} catch (AbortException $e) {
			throw $e;

		} catch (OAuthServerException $e) {
			$this->onResponse($e->generateHttpResponse($response));

		} catch (\Exception $e) {
			if ($this->logger) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
			}
			$body = $this->createStream();
			$body->write('Unknown error');
			$this->onResponse($response->withStatus(HttpResponse::S500_INTERNAL_SERVER_ERROR)->withBody($body));
		}
	}

	/**
	 * @param AuthorizationRequest $authorizationRequest
	 */
	public function setAuthorizationRequest(AuthorizationRequest $authorizationRequest)
	{
		$this->authorizationRequest = $authorizationRequest;
	}

	/**
	 * @param string $file
	 */
	public function setTemplateFile(string $file)
	{
		$this->templateFile = $file;
	}
}
