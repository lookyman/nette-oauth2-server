<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests;

use Lookyman\NetteOAuth2Server\RedirectConfig;
use PHPUnit\Framework\TestCase;

class RedirectConfigTest extends TestCase
{

	public function testGet(): void
	{
		$config = new RedirectConfig('approve', 'login');
		self::assertIsArray($approve = $config->getApproveDestination());
		self::assertCount(1, $approve);
		self::assertEquals('approve', array_pop($approve));
		self::assertIsArray($login = $config->getLoginDestination());
		self::assertCount(1, $login);
		self::assertEquals('login', array_pop($login));
	}

	public function testEmptyApproveDestination(): void
	{
	    $this->expectException("\Nette\InvalidStateException");
		$config = new RedirectConfig(null, 'login');
		$config->getApproveDestination();
	}

	public function testEmptyLoginDestination(): void
	{
        $this->expectException("\Nette\InvalidStateException");

        $config = new RedirectConfig('approve', null);
		$config->getLoginDestination();
	}

}
