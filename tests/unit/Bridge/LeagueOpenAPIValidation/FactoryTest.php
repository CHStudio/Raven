<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Bridge\LeagueOpenAPIValidation;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\InvalidOpenApiDefinitionException;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Factory;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\RequestValidator;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\ResponseValidator;
use Exception;
use InvalidArgumentException;
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
            ->expects(self::once())
            ->method('getRequestValidator')
            ->willReturn($this->createMock(PSR7RequestValidator::class));
        $leagueBuilder
            ->expects(self::once())
            ->method('getResponseValidator')
            ->willReturn($this->createMock(PSR7ResponseValidator::class));

        $factory = new Factory($leagueBuilder);

        self::assertInstanceOf(RequestValidator::class, $factory->getRequestValidator());
        self::assertInstanceOf(ResponseValidator::class, $factory->getResponseValidator());
    }

    public function testItCaptureErrorsDuringRequestValidatorCreation(): void
    {
        $this->expectException(InvalidOpenApiDefinitionException::class);

        $leagueBuilder = $this->createMock(ValidatorBuilder::class);
        $leagueBuilder
            ->expects(self::once())
            ->method('getRequestValidator')
            ->willThrowException(new Exception('Anything happened there…'));

        (new Factory($leagueBuilder))->getRequestValidator();
    }

    public function testItCaptureErrorsDuringResponseValidatorCreation(): void
    {
        $this->expectException(InvalidOpenApiDefinitionException::class);

        $leagueBuilder = $this->createMock(ValidatorBuilder::class);
        $leagueBuilder
            ->expects(self::once())
            ->method('getResponseValidator')
            ->willThrowException(new Exception('Anything happened there…'));

        (new Factory($leagueBuilder))->getResponseValidator();
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
            self::markTestSkipped('Temp file wasn\'t written.');
            return;
        }

        $factory = Factory::fromYamlFile($file);

        self::assertInstanceOf(RequestValidator::class, $factory->getRequestValidator());
        self::assertInstanceOf(ResponseValidator::class, $factory->getResponseValidator());
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
            self::markTestSkipped('Temp file wasn\'t written.');
            return;
        }

        $factory = Factory::fromJsonFile($file);

        self::assertInstanceOf(RequestValidator::class, $factory->getRequestValidator());
        self::assertInstanceOf(ResponseValidator::class, $factory->getResponseValidator());
    }

    public function testItCantBeBuiltFromInexistantJsonFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Factory::fromJsonFile('/a/fake/path');
    }

    public function testItCantBeBuiltFromInexistantYamlFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Factory::fromYamlFile('/a/fake/path');
    }
}
