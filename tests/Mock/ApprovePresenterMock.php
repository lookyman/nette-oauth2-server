<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\Mock;

use Lookyman\Nette\OAuth2\Server\UI\ApproveControl;
use Lookyman\Nette\OAuth2\Server\UI\ApprovePresenterTrait;
use Nette\Application\UI\Presenter;

final class ApprovePresenterMock extends Presenter
{
	use ApprovePresenterTrait;

	public function getApproveComponent(): ApproveControl
	{
		return $this['approve'];
	}

}
