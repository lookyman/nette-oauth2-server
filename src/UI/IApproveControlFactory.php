<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\UI;

interface IApproveControlFactory
{
	/**
	 * @return ApproveControl
	 */
	public function create(): ApproveControl;
}
