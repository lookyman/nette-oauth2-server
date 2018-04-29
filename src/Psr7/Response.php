<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Psr7;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\NotImplementedException;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

class Response implements ApplicationPsr7ResponseInterface
{

	/**
	 * @var int
	 */
	private $code = IResponse::S200_OK;

	/**
	 * @var array
	 */
	private $headers = [];

	/**
	 * @var StreamInterface|null
	 */
	private $stream;

	public function send(IRequest $httpRequest, IResponse $httpResponse): void
	{
		$httpResponse->setCode($this->code);
		foreach ($this->headers as $name => $value) {
			$httpResponse->setHeader($name, $value);
		}
		echo (string) $this->getBody();
	}

	/**
	 * @param string $name
	 * @param string|string[] $value
	 * @return self
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function withHeader($name, $value)
	{
		$response = clone $this;
		$response->headers[$name] = $value;
		return $response;
	}

	public function getBody(): StreamInterface
	{
		if ($this->stream === null) {
			$this->stream = new Stream('php://temp', 'r+');
		}
		return $this->stream;
	}

	/**
	 * @param int $code
	 * @param string $reasonPhrase
	 * @return self
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function withStatus($code, $reasonPhrase = '')
	{
		$response = clone $this;
		$response->code = $code;
		return $response;
	}

	/**
	 * @return self
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function withBody(StreamInterface $body)
	{
		$response = clone $this;
		$response->stream = $body;
		return $response;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function getProtocolVersion()
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function withProtocolVersion($version)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function getHeaders()
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function hasHeader($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function getHeader($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function getHeaderLine($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function withAddedHeader($name, $value)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function withoutHeader($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function getStatusCode()
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function getReasonPhrase()
	{
		throw new NotImplementedException();
	}

}
