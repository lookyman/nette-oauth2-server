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
	 * @var StreamInterface
	 */
	private $stream;

	/**
	 * @param IRequest $httpRequest
	 * @param IResponse $httpResponse
	 * @return void
	 */
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
	 * @return self
	 */
	public function withHeader($name, $value)
	{
		$response = clone $this;
		$response->headers[$name] = $value;
		return $response;
	}

	/**
	 * @return StreamInterface
	 */
	public function getBody()
	{
		if (!$this->stream) {
			$this->stream = new Stream('php://temp', 'r+');
		}
		return $this->stream;
	}

	/**
	 * @param int $code
	 * @param string $reasonPhrase
	 * @return self
	 */
	public function withStatus($code, $reasonPhrase = '')
	{
		$response = clone $this;
		$response->code = $code;
		return $response;
	}

	/**
	 * @return self
	 */
	public function withBody(StreamInterface $body)
	{
		$response = clone $this;
		$response->stream = $body;
		return $response;
	}

	/**
	 * @throws NotImplementedException
	 */
	public function getProtocolVersion()
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function withProtocolVersion($version)
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function getHeaders()
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function hasHeader($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function getHeader($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function getHeaderLine($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function withAddedHeader($name, $value)
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function withoutHeader($name)
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function getStatusCode()
	{
		throw new NotImplementedException();
	}

	/**
	 * @throws NotImplementedException
	 */
	public function getReasonPhrase()
	{
		throw new NotImplementedException();
	}

}
