<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Expectation\ExpectationFailedException;
use CHStudio\Raven\Validator\Expectation\ResponseExpectationInterface;
use CHStudio\Raven\Validator\Expectation\StatusCode;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class StatusCodeTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $expectation = new StatusCode(200);

        static::assertInstanceOf(ResponseExpectationInterface::class, $expectation);
    }

    public function testItVerifiesStatusCode(): void
    {
        $expectation = new StatusCode(200);
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->expects(static::once())
            ->method('getStatusCode')
            ->willReturn(200);

        static::assertNull($expectation->verify($response));
    }

    public function testItReturnsExceptionIfFailed(): void
    {
        $expectation = new StatusCode(400);
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->expects(static::exactly(2))
            ->method('getStatusCode')
            ->willReturn(200);

        $result = $expectation->verify($response);
        static::assertInstanceOf(ExpectationFailedException::class, $result);
        static::assertSame(
            'Unexpected status code 200, expected 400',
            $result->getMessage()
        );
    }
}
