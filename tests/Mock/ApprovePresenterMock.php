<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\Mock;

use Lookyman\NetteOAuth2Server\UI\ApproveControl;
use Lookyman\NetteOAuth2Server\UI\ApprovePresenterTrait;
use Nette\Application\UI\Presenter;

class ApprovePresenterMock extends Presenter
{

	use ApprovePresenterTrait;

	public function getApproveComponent(): ApproveControl
	{
		return $this['approve'];
	}

}
