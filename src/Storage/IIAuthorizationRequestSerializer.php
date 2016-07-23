<?php

namespace Lookyman\NetteOAuth2Server\Storage;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

interface IAuthorizationRequestSerializer
{
	/**
	* @param AuthorizationRequest $authorizationRequest
	* @return string
	*/
	public function serialize(AuthorizationRequest $authorizationRequest): string;

	/**
	* @param string $data
	* @return AuthorizationRequest
	*/
	public function unserialize(string $data): AuthorizationRequest;
}
