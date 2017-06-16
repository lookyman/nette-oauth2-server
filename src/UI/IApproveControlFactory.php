<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

interface IApproveControlFactory
{

	public function create(AuthorizationRequest $authorizationRequest): ApproveControl;
}
