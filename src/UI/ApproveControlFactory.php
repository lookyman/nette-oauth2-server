<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\UI;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Nette\Http\Session;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class ApproveControlFactory implements IApproveControlFactory, LoggerAwareInterface
{
	use LoggerAwareTrait;

	/**
	 * @var AuthorizationServer
	 */
	private $authorizationServer;

	/**
	 * @var Session
	 */
	private $session;

	public function __construct(AuthorizationServer $authorizationServer, Session $session)
	{
		$this->authorizationServer = $authorizationServer;
		$this->session = $session;
	}

	public function create(AuthorizationRequest $authorizationRequest): ApproveControl
	{
		$control = new ApproveControl($this->authorizationServer, $this->session, $authorizationRequest);
		if ($this->logger) {
			$control->setLogger($this->logger);
		}
		return $control;
	}

}
