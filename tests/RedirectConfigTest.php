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
		self::assertInternalType('array', $approve = $config->getApproveDestination());
		self::assertCount(1, $approve);
		self::assertEquals('approve', array_pop($approve));
		self::assertInternalType('array', $login = $config->getLoginDestination());
		self::assertCount(1, $login);
		self::assertEquals('login', array_pop($login));
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testEmptyApproveDestination(): void
	{
		$config = new RedirectConfig(null, 'login');
		$config->getApproveDestination();
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testEmptyLoginDestination(): void
	{
		$config = new RedirectConfig('approve', null);
		$config->getLoginDestination();
	}

}
