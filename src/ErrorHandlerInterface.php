<?php

declare(strict_types=1);

namespace Aidphp\Error;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ErrorHandlerInterface
{
    function handleError(Throwable $e, ServerRequestInterface $req = null): ResponseInterface;
}