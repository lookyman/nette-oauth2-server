<?php

namespace Lookyman\NetteOAuth2Server\UI;

use Lookyman\NetteOAuth2Server\RedirectConfig;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Http\IResponse;

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
}
