<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Bridge\LeagueOpenAPIValidation;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\RequestValidator;
use CHStudio\Raven\Validator\Exception\ApiSchemaException;
use CHStudio\Raven\Validator\Exception\OperationNotFoundException;
use CHStudio\Raven\Validator\RequestValidatorInterface;
use Exception;
use League\OpenAPIValidation\PSR7\Exception\MultipleOperationsMismatchForRequest;
use League\OpenAPIValidation\PSR7\Exception\NoOperation;
use League\OpenAPIValidation\PSR7\Exception\NoPath;
use League\OpenAPIValidation\PSR7\RequestValidator as PSR7RequestValidator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

final class RequestValidatorTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $requestValidator = $this->createMock(PSR7RequestValidator::class);

        $validator = new RequestValidator(
            $requestValidator,
            new ValidationExceptionMapper()
        );

        self::assertInstanceOf(RequestValidatorInterface::class, $validator);
    }

    public function testItCanValidateRequest(): void
    {
        $requestValidator = $this->createMock(PSR7RequestValidator::class);
        $request = $this->createMock(RequestInterface::class);

        $requestValidator
            ->expects(self::once())
            ->method('validate')
            ->with($request);

        (new RequestValidator(
            $requestValidator,
            new ValidationExceptionMapper()
        ))->validate($request);
    }

    public function testItCallsExceptionMapperOnThrowable(): void
    {
        $this->expectException(ApiSchemaException::class);

        $requestValidator = $this->createMock(PSR7RequestValidator::class);
        $validationMapper = $this->createMock(ValidationExceptionMapper::class);
        $request = $this->createMock(RequestInterface::class);

        $error = new Exception('Error');

        $requestValidator
            ->expects(self::once())
            ->method('validate')
            ->with($request)
            ->willThrowException($error);

        $validationMapper
            ->expects(self::once())
            ->method('map')
            ->with($error)
            ->willReturn(new ApiSchemaException($error));

        (new RequestValidator($requestValidator, $validationMapper))->validate($request);
    }

    /**
     * @dataProvider provideItCanCatchExceptionsCases
     */
    public function testItCanCatchExceptions(Throwable $error, string $exception): void
    {
        $this->expectException($exception);

        $requestValidator = $this->createMock(PSR7RequestValidator::class);
        $request = $this->createMock(RequestInterface::class);

        $requestValidator
            ->expects(self::once())
            ->method('validate')
            ->with($request)
            ->willThrowException($error);

        (new RequestValidator(
            $requestValidator,
            new ValidationExceptionMapper()
        ))->validate($request);
    }

    public static function provideItCanCatchExceptionsCases(): iterable
    {
        yield [new NoOperation('Message'), OperationNotFoundException::class];
        yield [new NoPath('Message'), OperationNotFoundException::class];
        yield [new MultipleOperationsMismatchForRequest('Message'), OperationNotFoundException::class];
        yield [new RuntimeException('Message'), RuntimeException::class];
    }
}
