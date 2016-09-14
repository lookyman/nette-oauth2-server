<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\UI;

use League\OAuth2\Server\AuthorizationServer;
use Lookyman\NetteOAuth2Server\Tests\Mock\ApprovePresenterMock;
use Lookyman\NetteOAuth2Server\UI\ApproveControlFactory;
use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Nette\Http\IRequest as HttpRequest;
use Nette\Http\IResponse as HttpResponse;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\User;

class ApprovePresenterTraitTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateComponentApprove()
	{
		$httpRequest = $this->getMockBuilder(HttpRequest::class)->disableOriginalConstructor()->getMock();
		$httpResponse = $this->getMockBuilder(HttpResponse::class)->disableOriginalConstructor()->getMock();

		$section = $this->getMockBuilder(SessionSection::class)->disableOriginalConstructor()->getMock();
		$section->expects(self::once())->method('remove');

		$session = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
		$session->expects(self::exactly(2))->method('getSection')->with(OAuth2Presenter::SESSION_NAMESPACE)->willReturn($section);

		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
		$user->expects(self::once())->method('isLoggedIn')->willReturn(true);

		$presenter = new ApprovePresenterMock();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $session, $user);
		$presenter->approveControlFactory = new ApproveControlFactory($this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock());

		$control = $presenter->getApproveComponent();
		foreach ($control->onAuthorizationComplete as $cb) {
			$cb();
		}
	}
}
