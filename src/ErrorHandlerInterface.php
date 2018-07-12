<?php

declare(strict_types=1);

namespace Aidphp\Error\Middleware;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ErrorHandlerInterface
{
    function handleError(Throwable $e, ServerRequestInterface $req): ResponseInterface;
}