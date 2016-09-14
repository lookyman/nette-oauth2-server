<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\User;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Nette\Security\User;

class UserRepository implements UserRepositoryInterface
{
	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var callable
	 */
	private $credentialsValidator;

	/**
	 * @param User $user
	 * @param callable|null $credentialsValidator
	 */
	public function __construct(User $user, callable $credentialsValidator = null)
	{
		$this->user = $user;
		$this->credentialsValidator = $credentialsValidator ?: function () {
			$this->user->logout(true);
			try {
				$this->user->login(...func_get_args());

			} catch (\Exception $e) {} // Fail silently

			return $this->user->isLoggedIn() ? new UserEntity($this->user->getId()) : null;
		};
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @param string $grantType
	 * @param ClientEntityInterface $clientEntity
	 * @return UserEntityInterface|null
	 */
	public function getUserEntityByUserCredentials(
		$username,
		$password,
		$grantType,
		ClientEntityInterface $clientEntity
	)
	{
		return call_user_func($this->credentialsValidator, $username, $password, $grantType, $clientEntity);
	}
}
