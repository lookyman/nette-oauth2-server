<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\UI;

use League\OAuth2\Server\AuthorizationServer;
use Lookyman\NetteOAuth2Server\UI\ApproveControl;
use Lookyman\NetteOAuth2Server\UI\ApproveControlFactory;
use Psr\Log\LoggerInterface;

class ApproveControlFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreate()
	{
		$factory = new ApproveControlFactory($this->getMockBuilder(AuthorizationServer::class)->disableOriginalConstructor()->getMock());
		$factory->setLogger($this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock());
		self::assertInstanceOf(ApproveControl::class, $factory->create());
	}
}
