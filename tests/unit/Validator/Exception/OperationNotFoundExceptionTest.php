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
            ->expects(self::once())
            ->method('__toString')
            ->willReturn('http://uri');

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn('GET');
        $request
            ->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        $exception = new OperationNotFoundException($request, new Exception('Error'));

        self::assertInstanceOf(ValidationException::class, $exception);
        self::assertStringContainsString('[GET] http://uri', $exception->getMessage());
    }
}
