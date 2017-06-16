<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\Psr7;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\NotImplementedException;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

final class Response implements ApplicationPsr7ResponseInterface
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
	 * @var StreamInterface
	 */
	private $stream;

	public function send(IRequest $httpRequest, IResponse $httpResponse)
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
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function withHeader($name, $value): Response
	{
		$response = clone $this;
		$response->headers[$name] = $value;
		return $response;
	}

	public function getBody(): StreamInterface
	{
		if (!$this->stream) {
			$this->stream = new Stream('php://temp', 'r+');
		}
		return $this->stream;
	}

	/**
	 * @param int $code
	 * @param string $reasonPhrase
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function withStatus($code, $reasonPhrase = ''): Response
	{
		$response = clone $this;
		$response->code = $code;
		return $response;
	}

	public function withBody(StreamInterface $body): Response
	{
		$response = clone $this;
		$response->stream = $body;
		return $response;
	}

	public function getProtocolVersion()
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function withProtocolVersion($version)
	{
		throw new NotImplementedException();
	}

	public function getHeaders()
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function hasHeader($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getHeader($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getHeaderLine($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function withAddedHeader($name, $value)
	{
		throw new NotImplementedException();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function withoutHeader($name)
	{
		throw new NotImplementedException();
	}

	public function getStatusCode()
	{
		throw new NotImplementedException();
	}

	public function getReasonPhrase()
	{
		throw new NotImplementedException();
	}

}
