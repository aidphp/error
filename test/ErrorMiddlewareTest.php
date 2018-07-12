<?php

declare(strict_types=1);

namespace Test\Aidphp\Error\Middleware;

use PHPUnit\Framework\TestCase;
use Aidphp\Error\Middleware\ErrorMiddleware;
use Aidphp\Error\Middleware\ErrorHandlerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Exception;

class ErrorMiddlewareTest extends TestCase
{
    protected $req;
    protected $res;
    protected $handler;

    public function setUp()
    {
        $this->req = $this->createMock(ServerRequestInterface::class);
        $this->res = $this->createMock(ResponseInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    public function testProcess()
    {
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->req)
            ->willReturn($this->res);

        $middleware = new ErrorMiddleware($this->createMock(ErrorHandlerInterface::class));
        $this->assertSame($this->res, $middleware->process($this->req, $this->handler));
    }

    public function testProcessWithException()
    {
        $e = new Exception();

        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->req)
            ->will($this->throwException($e));

        $errorHandler = $this->createMock(ErrorHandlerInterface::class);
        $errorHandler->expects($this->once())
            ->method('handleError')
            ->with($e, $this->req)
            ->willReturn($this->res);

        $middleware = new ErrorMiddleware($errorHandler);
        $this->assertSame($this->res, $middleware->process($this->req, $this->handler));
    }

    public function testProcessWithExceptionCleanOutputBuffer()
    {
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->req)
            ->willReturnCallback(function ($req) {
                ob_start();
                throw new Exception();
            });

        $errorHandler = $this->createMock(ErrorHandlerInterface::class);
        $errorHandler->expects($this->once())
            ->method('handleError')
            ->willReturn($this->res);

        $middleware = new ErrorMiddleware($errorHandler);
        $this->assertSame($this->res, $middleware->process($this->req, $this->handler));
    }
}