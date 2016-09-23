<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\Tests\Mock\ApprovePresenterMock;
use Lookyman\NetteOAuth2Server\UI\ApproveControlFactory;
use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\User;

class ApprovePresenterTraitTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateComponentApprove()
	{
		$request = serialize(new AuthorizationRequest());

		$serializer = $this->getMockBuilder(IAuthorizationRequestSerializer::class)->disableOriginalConstructor()->getMock();
		$serializer->expects(self::once())->method('unserialize')->with($request)->willReturn(unserialize($request));

		$httpRequest = $this->getMockBuilder(IRequest::class)->disableOriginalConstructor()->getMock();
		$httpResponse = $this->getMockBuilder(IResponse::class)->disableOriginalConstructor()->getMock();

		$section = $this->getMockBuilder(SessionSection::class)->disableOriginalConstructor()->getMock();
		$section->expects(self::once())->method('__get')->with('authorizationRequest')->willReturn($request);

		$session = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
		$session->expects(self::once())->method('getSection')->with(OAuth2Presenter::SESSION_NAMESPACE)->willReturn($section);

		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
		$user->expects(self::once())->method('isLoggedIn')->willReturn(true);
		$user->expects(self::once())->method('getId')->willReturn(1);

		$presenter = new ApprovePresenterMock();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $session, $user);
		$presenter->approveControlFactory = new ApproveControlFactory(
			$this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock(),
			$session
		);
		$presenter->authorizationRequestSerializer = $serializer;
		$presenter->getApproveComponent();
	}
}
