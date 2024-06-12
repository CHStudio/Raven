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

        self::assertInstanceOf(ValidationException::class, $exception);
        self::assertStringContainsString(var_export($value, true), $exception->getMessage());
        self::assertStringContainsString('p.a.th', $exception->getMessage());
        self::assertStringContainsString('Error', $exception->getMessage());
    }
}
