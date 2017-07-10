<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Nette\Http\Session;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Lookyman\Nette\OAuth2\Server\UI\ApproveControlFactory
 */
final class ApproveControlFactoryTest extends TestCase
{

	public function testCreate()
	{
		$factory = new ApproveControlFactory(
			$this->createMock(AuthorizationServer::class),
			$this->createMock(Session::class)
		);
		$factory->setLogger($this->createMock(LoggerInterface::class));
		self::assertInstanceOf(ApproveControl::class, $factory->create(new AuthorizationRequest()));
	}

}
