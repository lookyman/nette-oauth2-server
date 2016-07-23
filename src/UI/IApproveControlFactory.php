<?php

namespace Lookyman\NetteOAuth2Server\UI;

interface IApproveControlFactory
{
	/**
	 * @return ApproveControl
	 */
	public function create();
}
