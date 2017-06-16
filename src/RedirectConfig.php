<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server;

use Nette\Application\UI\Component;
use Nette\InvalidStateException;
use Nette\SmartObject;

/**
 * @method void onBeforeApproveRedirect(RedirectConfig $redirectConfig)
 * @method void onBeforeLoginRedirect(RedirectConfig $redirectConfig)
 */
final class RedirectConfig
{
	use SmartObject;

	/**
	 * @var callable[]
	 */
	public $onBeforeApproveRedirect = [];

	/**
	 * @var callable[]
	 */
	public $onBeforeLoginRedirect = [];

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
		$this->setApproveDestination($approveDestination);
		$this->setLoginDestination($loginDestination);
	}

	/**
	 * @param string|array|null $approveDestination
	 */
	public function setApproveDestination($approveDestination)
	{
		$this->approveDestination = (array) $approveDestination;
	}

	public function redirectToApproveDestination(Component $component)
	{
		if (empty($this->approveDestination)) {
			throw new InvalidStateException('Approve destination not set');
		}
		$this->onBeforeApproveRedirect($this);
		$component->redirect(...$this->approveDestination);
	}

	/**
	 * @param string|array|null $loginDestination
	 */
	public function setLoginDestination($loginDestination)
	{
		$this->loginDestination = (array) $loginDestination;
	}

	public function redirectToLoginDestination(Component $component)
	{
		if (empty($this->loginDestination)) {
			throw new InvalidStateException('Login destination not set');
		}
		$this->onBeforeLoginRedirect($this);
		$component->redirect(...$this->loginDestination);
	}
}
