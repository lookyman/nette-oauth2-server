<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\User;

use Kdyby\Events\Subscriber;
use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Nette\Application\Application;
use Nette\Application\IPresenter;
use Nette\Application\UI\Presenter;
use Nette\InvalidStateException;
use Nette\Security\User;

class LoginSubscriber implements Subscriber
{

	/**
	 * @var IPresenter|null
	 */
	private $presenter;

	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var RedirectConfig
	 */
	private $redirectConfig;

	public function __construct(RedirectConfig $redirectConfig, int $priority = 0)
	{
		$this->redirectConfig = $redirectConfig;
		$this->priority = $priority;
	}

	public function onPresenter(Application $application, IPresenter $presenter): void
	{
		$this->presenter = $presenter;
	}

	public function onLoggedIn(User $user): void
	{
		if ($this->presenter === null) {
			throw new InvalidStateException('Presenter not set');
		}
		if ($this->presenter instanceof Presenter && $this->presenter->getSession(OAuth2Presenter::SESSION_NAMESPACE)->authorizationRequest) {
			$this->presenter->redirect(...$this->redirectConfig->getApproveDestination());
		}
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents(): array
	{
		return [
			Application::class . '::onPresenter',
			User::class . '::onLoggedIn' => [
				['onLoggedIn', $this->priority],
			],
		];
	}

}
