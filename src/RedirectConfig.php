<?php

namespace Lookyman\NetteOAuth2Server;

class RedirectConfig
{
	/**
	 * @var array
	 */
	private $approveDestination;

	/**
	 * @var array
	 */
	private $loginDestination;

	/**
	 * @param string|array $approveDestination
	 * @param string|array $loginDestination
	 */
	public function __construct($approveDestination, $loginDestination)
	{
		$this->approveDestination = (array) $approveDestination;
		$this->loginDestination = (array) $loginDestination;
	}

	/**
	 * @return array
	 */
	public function getApproveDestination(): array
	{
		return $this->approveDestination;
	}

	/**
	 * @return array
	 */
	public function getLoginDestination(): array
	{
		return $this->loginDestination;
	}
}
