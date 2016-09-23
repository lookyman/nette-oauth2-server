<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

use Lookyman\NetteOAuth2Server\Psr7\ApplicationPsr7ResponseInterface;
use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\User\UserEntity;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\IResponse;
use Nette\Http\IResponse as HttpResponse;
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

	/**
	 * @return ApproveControl
	 * @throws AbortException
	 * @throws BadRequestException
	 */
	protected function createComponentApprove(): ApproveControl
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(...$this->redirectConfig->getLoginDestination());
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
	 * @param string|null $message
	 * @param int $code
	 * @throws BadRequestException
	 */
	abstract public function error($message = null, $code = HttpResponse::S404_NOT_FOUND);

	/**
	 * @param string|null $namespace
	 * @return Session|SessionSection
	 */
	abstract public function getSession($namespace = null);

	/**
	 * @return User
	 */
	abstract public function getUser();

	/**
	 * @param int $code [optional]
	 * @param string|null $destination
	 * @param array|mixed $args
	 * @throws AbortException
	 */
	abstract public function redirect($code, $destination = null, $args = []);

	/**
	 * @param IResponse $response
	 * @throws AbortException
	 */
	abstract public function sendResponse(IResponse $response);
}
