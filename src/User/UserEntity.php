<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\User;

use League\OAuth2\Server\Entities\UserEntityInterface;

final class UserEntity implements UserEntityInterface
{

	/**
	 * @var mixed
	 */
	private $identifier;

	/**
	 * @param mixed $identifier
	 */
	public function __construct($identifier)
	{
		$this->identifier = $identifier;
	}

	/**
	 * @return mixed
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

}
