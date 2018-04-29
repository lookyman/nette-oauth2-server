<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\UI\ApproveControl;
use Lookyman\NetteOAuth2Server\UI\ApproveControlFactory;
use Nette\Http\Session;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ApproveControlFactoryTest extends TestCase
{

	public function testCreate(): void
	{
		$factory = new ApproveControlFactory(
			$this->createMock(AuthorizationServer::class),
			$this->createMock(Session::class)
		);
		$factory->setLogger($this->createMock(LoggerInterface::class));
		self::assertInstanceOf(ApproveControl::class, $factory->create(new AuthorizationRequest()));
	}

}
