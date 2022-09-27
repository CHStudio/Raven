<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Exception\DataSchemaException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use Exception;
use PHPUnit\Framework\TestCase;

final class DataSchemaExceptionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $value = ['value'];
        $exception = new DataSchemaException(
            $value,
            'p.a.th',
            new Exception('Error')
        );

        static::assertInstanceOf(ValidationException::class, $exception);
        static::assertStringContainsString(var_export($value, true), $exception->getMessage());
        static::assertStringContainsString('p.a.th', $exception->getMessage());
        static::assertStringContainsString('Error', $exception->getMessage());
    }
}
