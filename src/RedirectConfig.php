<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server;

use Nette\InvalidStateException;

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
	 * @param string|array|null $approveDestination
	 * @param string|array|null $loginDestination
	 */
	public function __construct($approveDestination, $loginDestination)
	{
		$this->approveDestination = (array) $approveDestination;
		$this->loginDestination = (array) $loginDestination;
	}

	/**
	 * @return array
	 * @throws InvalidStateException
	 */
	public function getApproveDestination(): array
	{
		if (empty($this->approveDestination)) {
			throw new InvalidStateException('Approve destination not set');
		}
		return $this->approveDestination;
	}

	/**
	 * @return array
	 * @throws InvalidStateException
	 */
	public function getLoginDestination(): array
	{
		if (empty($this->loginDestination)) {
			throw new InvalidStateException('Login destination not set');
		}
		return $this->loginDestination;
	}
}
