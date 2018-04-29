<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\User;

use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Lookyman\NetteOAuth2Server\User\LoginSubscriber;
use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use Nette\Security\User;
use PHPUnit\Framework\TestCase;

class LoginSubscriberTest extends TestCase
{

	public function testGetSubscribedEvents(): void
	{
		$subscriber = new LoginSubscriber(
			$this->createMock(RedirectConfig::class),
			10
		);
		self::assertEquals([
			Application::class . '::onPresenter',
			User::class . '::onLoggedIn' => [
				['onLoggedIn', 10],
			],
		], $subscriber->getSubscribedEvents());
	}

	public function testOnLoggedIn(): void
	{
		$redirectConfig = $this->createMock(RedirectConfig::class);
		$redirectConfig->expects(self::once())->method('getApproveDestination')->willReturn(['destination']);

		$presenter = $this->createMock(Presenter::class);
		$presenter->expects(self::once())->method('getSession')->with(OAuth2Presenter::SESSION_NAMESPACE)->willReturn((object) ['authorizationRequest' => true]);
		$presenter->expects(self::once())->method('redirect')->with('destination');

		$user = $this->createMock(User::class);

		$subscriber = new LoginSubscriber($redirectConfig);
		$subscriber->onPresenter($this->createMock(Application::class), $presenter);
		$subscriber->onLoggedIn($user);
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testOnLoggedInNoPresenter(): void
	{
		$redirectConfig = $this->createMock(RedirectConfig::class);

		$user = $this->createMock(User::class);

		$subscriber = new LoginSubscriber($redirectConfig);
		$subscriber->onLoggedIn($user);
	}

}
