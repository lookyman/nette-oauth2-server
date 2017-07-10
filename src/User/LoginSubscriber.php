<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\User;

use Kdyby\Events\Subscriber;
use Lookyman\Nette\OAuth2\Server\UI\OAuth2Presenter;
use Lookyman\Nette\OAuth2\Server\UI\RedirectService;
use Nette\Application\Application;
use Nette\Application\IPresenter;
use Nette\Application\UI\Presenter;
use Nette\InvalidStateException;
use Nette\Security\User;

final class LoginSubscriber implements Subscriber
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
	 * @var \Lookyman\Nette\OAuth2\Server\UI\RedirectService
	 */
	private $redirectService;

	public function __construct(RedirectService $redirectConfig, int $priority = 0)
	{
		$this->redirectService = $redirectConfig;
		$this->priority = $priority;
	}

	public function onPresenter(Application $application, IPresenter $presenter)
	{
		$this->presenter = $presenter;
	}

	public function onLoggedIn(User $user)
	{
		if (!$this->presenter) {
			throw new InvalidStateException('Presenter not set');
		}
		if ($this->presenter instanceof Presenter && $this->presenter->getSession(OAuth2Presenter::SESSION_NAMESPACE)->authorizationRequest) {
			$this->redirectService->redirectToApproveDestination($this->presenter);
		}
	}

	public function getSubscribedEvents(): array
	{
		return [
			Application::class . '::onPresenter' => 'onPresenter',
			User::class . '::onLoggedIn' => [
				['onLoggedIn', $this->priority],
			],
		];
	}

}
