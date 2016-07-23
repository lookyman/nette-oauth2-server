<?php

namespace Lookyman\NetteOAuth2Server\Psr7;

use Nette\Application\IResponse;
use Psr\Http\Message\ResponseInterface;

interface ApplicationPsr7ResponseInterface extends IResponse, ResponseInterface
{
}
