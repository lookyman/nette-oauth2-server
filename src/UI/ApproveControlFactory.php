<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ApproveControlFactory implements IApproveControlFactory, LoggerAwareInterface
{
	use LoggerAwareTrait;

	/**
	 * @var AuthorizationServer
	 */
	private $authorizationServer;

	/**
	 * @param AuthorizationServer $authorizationServer
	 */
	public function __construct(AuthorizationServer $authorizationServer)
	{
		$this->authorizationServer = $authorizationServer;
	}

	/**
	 * @return ApproveControl
	 */
	public function create(): ApproveControl
	{
		$control = new ApproveControl($this->authorizationServer);
		if ($this->logger) {
			$control->setLogger($this->logger);
		}
		return $control;
	}
}
