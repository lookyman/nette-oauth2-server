<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

interface IAuthorizationRequestSerializer
{

	public function serialize(AuthorizationRequest $authorizationRequest): string;

	public function unserialize(string $data): AuthorizationRequest;

}
