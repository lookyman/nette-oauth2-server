<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\User\UserEntity;
use Nette\Application\BadRequestException;
use Nette\ComponentModel\IComponent;
use Nette\Http\IResponse;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\User;
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

	/**
	 * @param string|null $message
	 * @param int $code
	 * @throws BadRequestException
	 */
	abstract public function error($message = null, $code = IResponse::S404_NOT_FOUND);

	/**
	 * @param string $name
	 * @param bool $need
	 * @return IComponent|null mixed
	 */
	abstract public function getComponent($name, $need = true);

	/**
	 * @param string|null $namespace
	 * @return Session|SessionSection
	 */
	abstract public function getSession($namespace = null);

	/**
	 * @return User
	 */
	abstract public function getUser();
}
