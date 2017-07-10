<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use Lookyman\Nette\OAuth2\Server\Psr7\ApplicationPsr7ResponseInterface;
use Lookyman\Nette\OAuth2\Server\RedirectConfig;
use Lookyman\Nette\OAuth2\Server\Storage\IAuthorizationRequestSerializer;
use Lookyman\Nette\OAuth2\Server\User\UserEntity;
use Nette\Application\IResponse;
use Nette\Http\IResponse as HttpResponse;
use Nextras\Application\UI\SecuredLinksPresenterTrait;

trait ApprovePresenterTrait
{
	use SecuredLinksPresenterTrait;

	/**
	 * @var IApproveControlFactory
	 * @inject
	 */
	public $approveControlFactory;

	/**
	 * @var IAuthorizationRequestSerializer
	 * @inject
	 */
	public $authorizationRequestSerializer;

	/**
	 * @var RedirectConfig
	 * @inject
	 */
	public $redirectConfig;

	protected function createComponentApprove(): ApproveControl
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirectConfig->redirectToLoginDestination($this);
		}

		/** @var string $data */
		$data = $this->getSession(OAuth2Presenter::SESSION_NAMESPACE)->authorizationRequest;
		$authorizationRequest = $data ? $this->authorizationRequestSerializer->unserialize($data) : null;

		if ($authorizationRequest) {
			if (!$authorizationRequest->getUser()) {
				$authorizationRequest->setUser(new UserEntity($this->getUser()->getId()));
			}
			$control = $this->approveControlFactory->create($authorizationRequest);
			$control->onResponse[] = function (ApplicationPsr7ResponseInterface $response) {
				$this->sendResponse($response);
			};
			return $control;
		}

		$this->error(null, HttpResponse::S400_BAD_REQUEST);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	abstract public function error($message = null, $code = HttpResponse::S404_NOT_FOUND);

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	abstract public function getSession($namespace = null);

	abstract public function getUser();

	abstract public function sendResponse(IResponse $response);

}
