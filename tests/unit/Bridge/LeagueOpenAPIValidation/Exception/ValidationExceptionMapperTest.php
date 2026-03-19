<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Bridge\LeagueOpenAPIValidation\Exception;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use CHStudio\Raven\Validator\Exception\ApiSchemaException;
use CHStudio\Raven\Validator\Exception\DataSchemaException;
use CHStudio\Raven\Validator\Exception\GenericException;
use CHStudio\Raven\Validator\Exception\RequiredParameterMissingException;
use Exception;
use InvalidArgumentException;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidPath;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidQueryArgs;
use League\OpenAPIValidation\PSR7\Exception\Validation\RequiredParameterMissing;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\Schema\BreadCrumb;
use League\OpenAPIValidation\Schema\Exception\InvalidSchema;
use League\OpenAPIValidation\Schema\Exception\SchemaMismatch;
use PHPUnit\Framework\TestCase;

final class ValidationExceptionMapperTest extends TestCase
{
    public function testItDoesntMapUnknownException(): void
    {
        $exceptionChain = new Exception(
            'Message',
            0,
            new Exception(
                'Child message',
                0,
                new InvalidArgumentException('Error to not be captured', 0)
            )
        );

        self::assertNotInstanceOf(\CHStudio\Raven\Validator\Exception\ValidationException::class, (new ValidationExceptionMapper())->map($exceptionChain));
    }

    public function testItMapsSchemaMismatchException(): void
    {
        $exceptionChain = new Exception(
            'Message',
            0,
            new Exception(
                'Child message',
                0,
                $error = (new SchemaMismatch('Error to be captured', 0))
                    ->withBreadCrumb((new BreadCrumb('a'))->addCrumb('b'))
            )
        );

        $mapped = (new ValidationExceptionMapper())->map($exceptionChain);

        self::assertInstanceOf(DataSchemaException::class, $mapped);
        self::assertSame('a.b', $mapped->path);
        self::assertSame($error, $mapped->getPrevious());
    }

    public function testItMapsInvalidSchema(): void
    {
        $exceptionChain = new Exception(
            'Message',
            0,
            new Exception(
                'Child message',
                0,
                $error = new InvalidSchema('Error to be captured', 0)
            )
        );

        $mapped = (new ValidationExceptionMapper())->map($exceptionChain);

        self::assertInstanceOf(ApiSchemaException::class, $mapped);
        self::assertSame($error, $mapped->getPrevious());
    }

    public function testItMapsValidationFailed(): void
    {
        $exceptionChain = new Exception(
            'Message',
            0,
            new Exception(
                'Child message',
                0,
                new ValidationFailed('Error to be captured', 0)
            )
        );

        $mapped = (new ValidationExceptionMapper())->map($exceptionChain);

        self::assertInstanceOf(GenericException::class, $mapped);
    }

    public function testItMapsInvalidQueryArgs(): void
    {
        $operationAddress = new OperationAddress('/path', 'GET');
        $invalidQueryArgsError = InvalidQueryArgs::becauseOfMissingRequiredArgument(
            'argument',
            $operationAddress,
            InvalidPath::fromAddr(new OperationAddress('path', 'method'))
        );

        $exceptionChain = new Exception(
            'Message',
            0,
            new Exception(
                'Child message',
                0,
                $invalidQueryArgsError
            )
        );

        $mapped = (new ValidationExceptionMapper())->map($exceptionChain);

        self::assertInstanceOf(GenericException::class, $mapped);
        self::assertSame($invalidQueryArgsError, $mapped->getPrevious());
    }

    public function testItMapsRequiredParameterMissing(): void
    {
        $exceptionChain = RequiredParameterMissing::fromName('missingParameter');
        $mapped = (new ValidationExceptionMapper())->map($exceptionChain);

        self::assertInstanceOf(RequiredParameterMissingException::class, $mapped);
    }
}
