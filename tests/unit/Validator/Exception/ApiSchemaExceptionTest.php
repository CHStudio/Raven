<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Exception\ApiSchemaException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use Exception;
use PHPUnit\Framework\TestCase;

final class ApiSchemaExceptionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $exception = new ApiSchemaException(new Exception('Error'));

        self::assertInstanceOf(ValidationException::class, $exception);
        self::assertStringContainsString('Error', $exception->getMessage());
    }
}
