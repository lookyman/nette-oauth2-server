<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\UI\ApproveControl;
use Lookyman\NetteOAuth2Server\UI\ApproveControlFactory;
use Nette\Http\Session;
use Psr\Log\LoggerInterface;

class ApproveControlFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreate()
	{
		$factory = new ApproveControlFactory(
			$this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock()
		);
		$factory->setLogger($this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock());
		self::assertInstanceOf(ApproveControl::class, $factory->create(new AuthorizationRequest()));
	}
}
