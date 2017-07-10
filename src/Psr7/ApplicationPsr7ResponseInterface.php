<?php

declare(strict_types = 1);

namespace Lookyman\Nette\OAuth2\Server\Psr7;

use Nette\Application\IResponse;
use Psr\Http\Message\ResponseInterface;

interface ApplicationPsr7ResponseInterface extends IResponse, ResponseInterface
{
}
