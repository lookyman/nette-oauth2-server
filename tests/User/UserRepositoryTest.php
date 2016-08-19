<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\User;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Lookyman\NetteOAuth2Server\User\UserEntity;
use Lookyman\NetteOAuth2Server\User\UserRepository;
use Nette\Security\User;

class UserRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetUserEntityByUserCredentials()
	{
		$client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
		$user->expects(self::once())->method('login')->with('name', 'passwd', 'grant', $client);
		$user->expects(self::once())->method('isLoggedIn')->willReturn(true);
		$user->expects(self::once())->method('getId')->willReturn('id');
		$repository = new UserRepository($user);
		$entity = $repository->getUserEntityByUserCredentials('name', 'passwd', 'grant', $client);
		self::assertInstanceOf(UserEntity::class, $entity);
		self::assertEquals('id', $entity->getIdentifier());
	}

	public function testGetUserEntityByUserCredentialsInvalidCredentials()
	{
		$client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
		$user->expects(self::once())->method('login')->with('name', 'passwd', 'grant', $client);
		$user->expects(self::once())->method('isLoggedIn')->willReturn(false);
		$repository = new UserRepository($user);
		$entity = $repository->getUserEntityByUserCredentials('name', 'passwd', 'grant', $client);
		self::assertNull($entity);
	}

	public function testGetUserEntityByUserCredentialsException()
	{
		$client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
		$user->expects(self::once())->method('login')->with('name', 'passwd', 'grant', $client)->willThrowException(new \Exception());
		$user->expects(self::once())->method('isLoggedIn')->willReturn(false);
		$repository = new UserRepository($user);
		$entity = $repository->getUserEntityByUserCredentials('name', 'passwd', 'grant', $client);
		self::assertNull($entity);
	}

	public function testCustomValidator()
	{
		$client = $this->getMockBuilder(ClientEntityInterface::class)->getMock();
		$user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
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
