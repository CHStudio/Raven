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

        self::assertInstanceOf(ResponseExpectationInterface::class, $expectation);
    }

    public function testItVerifiesStatusCode(): void
    {
        $expectation = new StatusCode(200);
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(200);

        self::assertNull($expectation->verify($response));
    }

    public function testItReturnsExceptionIfFailed(): void
    {
        $expectation = new StatusCode(400);
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->expects(self::exactly(2))
            ->method('getStatusCode')
            ->willReturn(200);

        $result = $expectation->verify($response);
        self::assertInstanceOf(ExpectationFailedException::class, $result);
        self::assertSame(
            'Unexpected status code 200, expected 400',
            $result->getMessage()
        );
    }
}
