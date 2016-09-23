<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

interface IApproveControlFactory
{
	/**
	 * @param AuthorizationRequest $authorizationRequest
	 * @return ApproveControl
	 */
	public function create(AuthorizationRequest $authorizationRequest): ApproveControl;
}
