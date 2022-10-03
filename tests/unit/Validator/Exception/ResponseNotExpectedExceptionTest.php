<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Exception\ResponseNotExpectedException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class ResponseNotExpectedExceptionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects(static::once())
            ->method('__toString')
            ->willReturn('http://uri');

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects(static::once())
            ->method('getMethod')
            ->willReturn('GET');
        $request
            ->expects(static::once())
            ->method('getUri')
            ->willReturn($uri);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(static::once())
            ->method('getStatusCode')
            ->willReturn(450);

        $exception = new ResponseNotExpectedException($request, $response, new Exception('Error'));

        static::assertInstanceOf(ValidationException::class, $exception);
        static::assertStringContainsString('[GET] http://uri', $exception->getMessage());
        static::assertStringContainsString('450', $exception->getMessage());
    }
}
