<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Exception\GenericException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use Exception;
use PHPUnit\Framework\TestCase;

final class GenericExceptionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $exception = new GenericException(new Exception('Error'));

        static::assertInstanceOf(ValidationException::class, $exception);
        static::assertStringContainsString('Something went wrong:', $exception->getMessage());
        static::assertStringContainsString('Error', $exception->getMessage());
    }
}
