<?php

namespace Lookyman\NetteOAuth2Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\User\UserEntity;
use Nette\Http\IResponse;
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

	protected function initializeApproveComponent()
	{
		$this->getComponent('approve');
	}

	/**
	 * @return ApproveControl
	 */
	protected function createComponentApprove()
	{
		$control = $this->approveControlFactory->create();

		$control->onAnchor[] = function (ApproveControl $control) {
			if (!$this->redirectConfig) {
				$this->error('RedirectConfig not set', IResponse::S500_INTERNAL_SERVER_ERROR);
			}
			if (!$this->getUser()->isLoggedIn()) {
				call_user_func_array([$this, 'redirect'], $this->redirectConfig->getLoginDestination());
			}

			$data = $this->getSession(OAuth2Presenter::SESSION_NAMESPACE)->authorizationRequest;
			$authorizationRequest = $data ? $this->authorizationRequestSerializer->unserialize($data) : null;
			if (!$authorizationRequest) {
				$this->error('No authorization request in session', IResponse::S400_BAD_REQUEST);
			}

			if (!$authorizationRequest->getUser()) {
				$authorizationRequest->setUser(new UserEntity($this->getUser()->getId()));
			}
			$control->setAuthorizationRequest($authorizationRequest);

			if ($authorizationRequest->isAuthorizationApproved()) {
				$control->completeAuthorizationRequest();
			}
		};

		return $control;
	}
}
