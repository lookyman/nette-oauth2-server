<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\Nette\OAuth2\Server\Mock\ApprovePresenterMock;
use Lookyman\Nette\OAuth2\Server\Storage\IAuthorizationRequestSerializer;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lookyman\Nette\OAuth2\Server\UI\ApprovePresenterTrait
 */
final class ApprovePresenterTraitTest extends TestCase
{

	public function testCreateComponentApprove()
	{
		$request = serialize(new AuthorizationRequest());

		$serializer = $this->createMock(IAuthorizationRequestSerializer::class);
		$serializer->expects(self::once())->method('unserialize')->with($request)->willReturn(unserialize($request));

		$httpRequest = $this->createMock(IRequest::class);
		$httpResponse = $this->createMock(IResponse::class);

		$section = $this->createMock(SessionSection::class);
		$section->expects(self::once())->method('__get')->with('authorizationRequest')->willReturn($request);

		$session = $this->createMock(Session::class);
		$session->expects(self::once())->method('getSection')->with(OAuth2Presenter::SESSION_NAMESPACE)->willReturn($section);

		$user = $this->createMock(User::class);
		$user->expects(self::once())->method('isLoggedIn')->willReturn(true);
		$user->expects(self::once())->method('getId')->willReturn(1);

		$presenter = new ApprovePresenterMock();
		$presenter->injectPrimary(null, null, null, $httpRequest, $httpResponse, $session, $user);
		$presenter->approveControlFactory = new ApproveControlFactory(
			$this->createMock(AuthorizationServer::class),
			$session
		);
		$presenter->authorizationRequestSerializer = $serializer;
		$presenter->getApproveComponent();
	}

}
