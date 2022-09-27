<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Bridge\LeagueOpenAPIValidation;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Factory;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\RequestValidator;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\ResponseValidator;
use League\OpenAPIValidation\PSR7\RequestValidator as PSR7RequestValidator;
use League\OpenAPIValidation\PSR7\ResponseValidator as PSR7ResponseValidator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    public function testItCanBuildValidators(): void
    {
        $leagueBuilder = $this->createMock(ValidatorBuilder::class);
        $leagueBuilder
            ->expects(static::once())
            ->method('getRequestValidator')
            ->willReturn($this->createMock(PSR7RequestValidator::class));
        $leagueBuilder
            ->expects(static::once())
            ->method('getResponseValidator')
            ->willReturn($this->createMock(PSR7ResponseValidator::class));

        $factory = new Factory($leagueBuilder);

        static::assertInstanceOf(RequestValidator::class, $factory->getRequestValidator());
        static::assertInstanceOf(ResponseValidator::class, $factory->getResponseValidator());
    }

    public function testItCanBeBuiltFromYamlFile(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'testItCanBeBuiltFromYamlFile');
        $writeSuccess = file_put_contents($file, <<<YAML
        openapi: 3.0.3
        info:
          title: OpenAPI ?
        YAML);

        if ($writeSuccess === false) {
            static::markTestSkipped('Temp file wasn\'t written.');
            return;
        }

        $factory = Factory::fromYamlFile($file);

        static::assertInstanceOf(RequestValidator::class, $factory->getRequestValidator());
        static::assertInstanceOf(ResponseValidator::class, $factory->getResponseValidator());
    }

    public function testItCanBeBuiltFromJsonFile(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'testItCanBeBuiltFromJsonFile');
        $writeSuccess = file_put_contents($file, <<<JSON
            {
                "openapi": "3.0.0",
                "info": {
                    "title": "OpenAPI ?"
                }
            }
        JSON);

        if ($writeSuccess === false) {
            static::markTestSkipped('Temp file wasn\'t written.');
            return;
        }

        $factory = Factory::fromJsonFile($file);

        static::assertInstanceOf(RequestValidator::class, $factory->getRequestValidator());
        static::assertInstanceOf(ResponseValidator::class, $factory->getResponseValidator());
    }
}
