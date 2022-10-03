<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Exception\OperationNotFoundException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use Exception;
use League\Uri\Contracts\UriInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class OperationNotFoundExceptionTest extends TestCase
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

        $exception = new OperationNotFoundException($request, new Exception('Error'));

        static::assertInstanceOf(ValidationException::class, $exception);
        static::assertStringContainsString('[GET] http://uri', $exception->getMessage());
    }
}
