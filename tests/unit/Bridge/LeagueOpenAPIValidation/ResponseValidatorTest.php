<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Bridge\LeagueOpenAPIValidation;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\Paths;
use cebe\openapi\spec\Server;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\ResponseValidator;
use CHStudio\Raven\Validator\Exception\GenericException;
use CHStudio\Raven\Validator\Exception\OperationNotFoundException;
use CHStudio\Raven\Validator\Exception\ResponseNotExpectedException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use CHStudio\Raven\Validator\ResponseValidatorInterface;
use InvalidArgumentException;
use League\OpenAPIValidation\PSR7\Exception\NoResponseCode;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ResponseValidator as PSR7ResponseValidator;
use League\Uri\Contracts\UriInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

final class ResponseValidatorTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $responseValidator = $this->createMock(PSR7ResponseValidator::class);

        $validator = new ResponseValidator(
            $responseValidator,
            new ValidationExceptionMapper()
        );

        static::assertInstanceOf(ResponseValidatorInterface::class, $validator);
    }

    public function testItCanValidateResponse(): void
    {
        [, $request, $response, $responseValidator] = $this->prepare();

        $responseValidator
            ->expects(static::once())
            ->method('validate')
            ->with(
                static::callback(fn (OperationAddress $op) => $op->method() === 'get' && $op->path() === '/'),
                $response
            );

        $validator = new ResponseValidator(
            $responseValidator,
            new ValidationExceptionMapper()
        );

        $validator->validate($response, $request);
    }

    public function testItValidatesAgainstThePathThatMatchesTheBestTheCurrentRequest(): void
    {
        [$uri, $request, $response, $responseValidator] = $this->prepare(
            new OpenApi([
                'paths' => new Paths([
                    '/api/path' => new PathItem([
                        'get' => new Operation([])
                    ]),
                    '/api/{pattern}' => new PathItem([
                        'get' => new Operation([])
                    ])
                ]),
                'servers' => [
                    new Server(['url' => '/'])
                ]
            ]),
            'https://chstudio.fr/api/path'
        );

        $uri
            ->expects(static::atLeastOnce())
            ->method('getPath')
            ->willReturn('/api/path');

        $responseValidator
            ->expects(static::once())
            ->method('validate')
            ->with(
                static::callback(fn (OperationAddress $op) => $op->method() === 'get' && $op->path() === '/api/path'),
                $response
            );

        $validator = new ResponseValidator(
            $responseValidator,
            new ValidationExceptionMapper()
        );

        $validator->validate($response, $request);
    }

    public function testItFailsWhenNoOperationHaveBeenFoundThatMatchesTheCurrentRequest(): void
    {
        $this->expectException(OperationNotFoundException::class);
        $this->expectExceptionMessageMatches('/\[GET\] https:\/\/chstudio\.fr\/anotherpath/');

        [$uri, $request, $response, $responseValidator] = $this->prepare(
            new OpenApi([
                'paths' => new Paths([
                    '/api/path' => new PathItem([
                        'get' => new Operation([])
                    ]),
                    '/' => new PathItem([
                        'get' => new Operation([])
                    ])
                ]),
                'servers' => [
                    new Server(['url' => '/'])
                ]
            ]),
            'https://chstudio.fr/anotherpath'
        );
        $uri
            ->expects(static::never())
            ->method('getPath');

        $responseValidator
            ->expects(static::never())
            ->method('validate');

        $validator = new ResponseValidator(
            $responseValidator,
            new ValidationExceptionMapper()
        );

        $validator->validate($response, $request);
    }

    public function testItFailsWhenNoResponseWasFoundThatMatchesStatusCode(): void
    {
        $this->expectException(ResponseNotExpectedException::class);
        $this->expectExceptionMessageMatches('/API response with status code 0 isn\'t defined in the spec for request/');

        [, $request, $response, $responseValidator] = $this->prepare();

        $responseValidator
            ->expects(static::once())
            ->method('validate')
            ->with(
                static::callback(fn (OperationAddress $op) => $op->method() === 'get' && $op->path() === '/'),
                $response
            )
            ->willThrowException(new NoResponseCode('Error'));

        $validator = new ResponseValidator(
            $responseValidator,
            new ValidationExceptionMapper()
        );

        $validator->validate($response, $request);
    }

    public function testItFailsWhenExceptionThrownDuringValidation(): void
    {
        $this->expectException(ValidationException::class);

        [, $request, $response, $responseValidator] = $this->prepare();

        $error = new RuntimeException('Message');

        $mapper = $this->createMock(ValidationExceptionMapper::class);
        $mapper
            ->expects(static::once())
            ->method('map')
            ->with($error)
            ->willReturn(new GenericException($error));

        $responseValidator
            ->expects(static::once())
            ->method('validate')
            ->with(
                static::callback(fn (OperationAddress $op) => $op->method() === 'get' && $op->path() === '/'),
                $response
            )
            ->willThrowException($error);

        $validator = new ResponseValidator($responseValidator, $mapper);

        $validator->validate($response, $request);
    }

    public function testItThrowsTheExceptionIfItCantBeMapped(): void
    {
        $this->expectException(InvalidArgumentException::class);

        [, $request, $response, $responseValidator] = $this->prepare();

        $responseValidator
            ->expects(static::once())
            ->method('validate')
            ->with(
                static::callback(fn (OperationAddress $op) => $op->method() === 'get' && $op->path() === '/'),
                $response
            )
            ->willThrowException(new InvalidArgumentException('Message'));

        $validator = new ResponseValidator(
            $responseValidator,
            new ValidationExceptionMapper()
        );

        $validator->validate($response, $request);
    }

    public function testItCapturesSpecificSpecFinderErrorsFromLeagueLibraryAsResponseNotFound(): void
    {
        $this->expectException(ResponseNotExpectedException::class);

        [, $request, $response, $responseValidator] = $this->prepare();

        $specFinderError = $this->createMock(Throwable::class);
        // A bit hacky but getFile is a final method.
        $reflection = new \ReflectionObject($specFinderError);
        $property = $reflection->getProperty('file');
        $property->setAccessible(true);
        $property->setValue($specFinderError, '/a/path/to/SpecFinder.php');

        $responseValidator
            ->expects(static::once())
            ->method('validate')
            ->with(
                static::callback(fn (OperationAddress $op) => $op->method() === 'get' && $op->path() === '/'),
                $response
            )
            ->willThrowException($specFinderError);

        $validator = new ResponseValidator(
            $responseValidator,
            new ValidationExceptionMapper()
        );

        $validator->validate($response, $request);
    }

    private function prepare(OpenApi $openApi = null, string $uriString = null): array
    {
        $openApi = $openApi ?? new OpenApi([
            'paths' => new Paths([
                '/' => new PathItem([
                    'get' => new Operation([])
                ])
            ]),
            'servers' => [
                new Server(['url' => '/'])
            ]
        ]);

        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects(static::atLeastOnce())
            ->method('__toString')
            ->willReturn($uriString ?? 'https://chstudio.fr/');
        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects(static::atLeastOnce())
            ->method('getUri')
            ->willReturn($uri);
        $request
            ->expects(static::atLeastOnce())
            ->method('getMethod')
            ->willReturn('GET');
        $response = $this->createMock(ResponseInterface::class);

        $responseValidator = $this->createMock(PSR7ResponseValidator::class);
        $responseValidator
            ->expects(static::once())
            ->method('getSchema')
            ->willReturn($openApi);

        return [$uri, $request, $response, $responseValidator];
    }
}
