<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\User;

use Lookyman\NetteOAuth2Server\User\UserEntity;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{

	public function testIdentifier(): void
	{
		$entity = new UserEntity('test');
		self::assertEquals('test', $entity->getIdentifier());
	}

}
