<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\User;

use Lookyman\NetteOAuth2Server\User\UserEntity;

class UserEntityTest extends \PHPUnit_Framework_TestCase
{
	public function testIdentifier()
	{
		$entity = new UserEntity('test');
		self::assertEquals('test', $entity->getIdentifier());
	}
}
