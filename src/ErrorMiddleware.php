<?php

declare(strict_types=1);

namespace Aidphp\Error;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorMiddleware implements MiddlewareInterface
{
    protected $errorHandler;

    public function __construct(ErrorHandlerInterface $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        $level = ob_get_level();

        try
        {
            $res = $handler->handle($req);
        }
        catch (Throwable $e)
        {
            while (ob_get_level() > $level)
            {
                ob_end_clean();
            }

            $res = $this->errorHandler->handleError($e, $req);
        }

        return $res;
    }
}