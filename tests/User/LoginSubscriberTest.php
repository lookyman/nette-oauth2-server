<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\User;

use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Lookyman\NetteOAuth2Server\User\LoginSubscriber;
use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use Nette\Security\User;

class LoginSubscriberTest extends \PHPUnit_Framework_TestCase
{
	public function testGetSubscribedEvents()
	{
		$subscriber = new LoginSubscriber(
			$this->getMockBuilder(RedirectConfig::class)->disableOriginalConstructor()->getMock(),
			10
		);
		self::assertEquals([
			Application::class . '::onPresenter',
			User::class . '::onLoggedIn' => [
				['onLoggedIn', 10],
			]
		], $subscriber->getSubscribedEvents());
	}

	public function testOnLoggedIn()
	{
		self::markTestSkipped('fixme');
		/*$redirectConfig = $this->getMockBuilder(RedirectConfig::class)->disableOriginalConstructor()->getMock();
		$redirectConfig->expects(self::once())->method('getApproveDestination')->willReturn(['destination']);

		$presenter = $this->getMockBuilder(Presenter::class)->disableOriginalConstructor()->getMock();
		$presenter->expects(self::once())->method('getSession')->with(OAuth2Presenter::SESSION_NAMESPACE)->willReturn((object) ['authorizationRequest' => true]);
		$presenter->expects(self::once())->method('redirect')->with('destination');

		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

		$subscriber = new LoginSubscriber($redirectConfig);
		$subscriber->onPresenter($this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock(), $presenter);
		$subscriber->onLoggedIn($user);*/
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testOnLoggedInNoPresenter()
	{
		$redirectConfig = $this->getMockBuilder(RedirectConfig::class)->disableOriginalConstructor()->getMock();

		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

		$subscriber = new LoginSubscriber($redirectConfig);
		$subscriber->onLoggedIn($user);
	}
}
