<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Bridge\LeagueOpenAPIValidation\Exception;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\InvalidOpenApiDefinitionException;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InvalidOpenApiDefinitionExceptionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $exception = new InvalidOpenApiDefinitionException(new Exception());

        self::assertInstanceOf(InvalidArgumentException::class, $exception);
        self::assertStringContainsString('The given OpenApi definition can\'t be loaded', $exception->getMessage());
    }
}
