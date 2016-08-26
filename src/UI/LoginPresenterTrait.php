<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

use Lookyman\NetteOAuth2Server\RedirectConfig;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Http\IResponse;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\User;

trait LoginPresenterTrait
{
	/**
	 * @var RedirectConfig
	 * @inject
	 */
	public $redirectConfig;

	/**
	 * @throws AbortException
	 * @throws BadRequestException
	 */
	protected function approveOAuth2Request()
	{
		if (!$this->redirectConfig) {
			$this->error('RedirectConfig not set', IResponse::S500_INTERNAL_SERVER_ERROR);
		}
		if ($this->getUser()->isLoggedIn() && $this->getSession(OAuth2Presenter::SESSION_NAMESPACE)->authorizationRequest) {
			call_user_func_array([$this, 'redirect'], $this->redirectConfig->getApproveDestination());
		}
	}

	/**
	 * @param string|null $message
	 * @param int $code
	 * @throws BadRequestException
	 */
	abstract public function error($message = null, $code = IResponse::S404_NOT_FOUND);

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
