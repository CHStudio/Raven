<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Exception;

use CHStudio\Raven\Validator\Exception\RequiredParameterMissingException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use League\OpenAPIValidation\PSR7\Exception\Validation\RequiredParameterMissing;
use PHPUnit\Framework\TestCase;

final class RequiredParameterMissingExceptionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $exception = new RequiredParameterMissingException(
            RequiredParameterMissing::fromName($missingParameterName = 'missingName')
        );

        self::assertInstanceOf(ValidationException::class, $exception);
        self::assertStringContainsString('A required parameter is missing:', $exception->getMessage());
        self::assertStringContainsString($missingParameterName, $exception->getMessage());
    }
}
