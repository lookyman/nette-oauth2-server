<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\User;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Lookyman\Nette\OAuth2\Server\User\UserEntity
 */
final class UserEntityTest extends TestCase
{

	public function testIdentifier()
	{
		$entity = new UserEntity('test');
		self::assertEquals('test', $entity->getIdentifier());
	}
}
