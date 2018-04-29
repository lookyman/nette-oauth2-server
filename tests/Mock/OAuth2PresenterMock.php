<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Tests\Mock;

use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Nette\Http\IResponse;

class OAuth2PresenterMock extends OAuth2Presenter
{

	/**
	 * @var callable
	 */
	private $callback;

	public function __construct(callable $callback)
	{
		parent::__construct();
		$this->callback = $callback;
	}

	/**
	 * @param int $code [optional]
	 * @param string|null $destination
	 * @param array $args
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function redirect($code, $destination = null, $args = [])
	{
		call_user_func($this->callback, $code, $destination, $args);
		$this->redirectUrl('', IResponse::S302_FOUND);
	}

}
