<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\User;

use Lookyman\Nette\OAuth2\Server\UI\OAuth2Presenter;
use Lookyman\Nette\OAuth2\Server\UI\RedirectService;
use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use Nette\Security\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lookyman\Nette\OAuth2\Server\User\LoginSubscriber
 */
final class LoginSubscriberTest extends TestCase
{

	public function testGetSubscribedEvents()
	{
		$subscriber = new LoginSubscriber(
			new RedirectService(null, null),
			10
		);
		self::assertEquals([
			Application::class . '::onPresenter' => 'onPresenter',
			User::class . '::onLoggedIn' => [
				['onLoggedIn', 10],
			],
		], $subscriber->getSubscribedEvents());
	}

	public function testOnLoggedIn()
	{
		$presenter = $this->createMock(Presenter::class);
		$presenter->expects(self::once())->method('getSession')->with(OAuth2Presenter::SESSION_NAMESPACE)->willReturn((object) ['authorizationRequest' => true]);
		$presenter->expects(self::once())->method('redirect')->with('destination');

		$subscriber = new LoginSubscriber(new RedirectService('destination', null));
		$subscriber->onPresenter($this->createMock(Application::class), $presenter);
		$subscriber->onLoggedIn($this->createMock(User::class));
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testOnLoggedInNoPresenter()
	{
		$subscriber = new LoginSubscriber(new RedirectService(null, null));
		$subscriber->onLoggedIn($this->createMock(User::class));
	}

}
