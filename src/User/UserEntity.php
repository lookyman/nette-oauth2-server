<?php

namespace Lookyman\NetteOAuth2Server\User;

use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
	/**
	 * @var mixed
	 */
	private $identifier;

	/**
	 * @param $identifier
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
