<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\User;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Nette\Security\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lookyman\Nette\OAuth2\Server\User\UserRepository
 */
final class UserRepositoryTest extends TestCase
{

	public function testGetUserEntityByUserCredentials()
	{
		$client = $this->createMock(ClientEntityInterface::class);
		$user = $this->createMock(User::class);
		$user->expects(self::once())->method('login')->with('name', 'passwd', 'grant', $client);
		$user->expects(self::once())->method('isLoggedIn')->willReturn(true);
		$user->expects(self::once())->method('getId')->willReturn('id');
		$repository = new UserRepository($user);
		/** @var UserEntityInterface $entity */
		$entity = $repository->getUserEntityByUserCredentials('name', 'passwd', 'grant', $client);
		self::assertInstanceOf(UserEntity::class, $entity);
		self::assertEquals('id', $entity->getIdentifier());
	}

	public function testGetUserEntityByUserCredentialsInvalidCredentials()
	{
		$client = $this->createMock(ClientEntityInterface::class);
		$user = $this->createMock(User::class);
		$user->expects(self::once())->method('login')->with('name', 'passwd', 'grant', $client);
		$user->expects(self::once())->method('isLoggedIn')->willReturn(false);
		$repository = new UserRepository($user);
		$entity = $repository->getUserEntityByUserCredentials('name', 'passwd', 'grant', $client);
		self::assertNull($entity);
	}

	public function testGetUserEntityByUserCredentialsException()
	{
		$client = $this->createMock(ClientEntityInterface::class);
		$user = $this->createMock(User::class);
		$user->expects(self::once())->method('login')->with('name', 'passwd', 'grant', $client)->willThrowException(new \Exception());
		$user->expects(self::once())->method('isLoggedIn')->willReturn(false);
		$repository = new UserRepository($user);
		$entity = $repository->getUserEntityByUserCredentials('name', 'passwd', 'grant', $client);
		self::assertNull($entity);
	}

	public function testCustomValidator()
	{
		$client = $this->createMock(ClientEntityInterface::class);
		$user = $this->createMock(User::class);
		$temp = false;
		$repository = new UserRepository($user, function ($username, $password, $grantType, $clientEntity) use ($client, &$temp) {
			self::assertEquals('name', $username);
			self::assertEquals('passwd', $password);
			self::assertEquals('grant', $grantType);
			self::assertSame($client, $clientEntity);
			$temp = true;
		});
		$entity = $repository->getUserEntityByUserCredentials('name', 'passwd', 'grant', $client);
		self::assertTrue($temp);
		self::assertNull($entity);
	}
}
