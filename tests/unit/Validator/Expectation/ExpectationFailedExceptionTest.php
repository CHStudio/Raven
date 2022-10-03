<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Expectation\ExpectationFailedException;
use CHStudio\Raven\Validator\Expectation\ResponseExpectationInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ExpectationFailedExceptionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $failedExpectation = $this->createMock(ResponseExpectationInterface::class);
        $exception = new ExpectationFailedException('message', $failedExpectation);

        static::assertInstanceOf(RuntimeException::class, $exception);
        static::assertSame($failedExpectation, $exception->expectation);
    }
}
