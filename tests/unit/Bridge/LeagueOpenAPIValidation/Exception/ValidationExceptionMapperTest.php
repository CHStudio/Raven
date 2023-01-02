<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Bridge\LeagueOpenAPIValidation\Exception;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use CHStudio\Raven\Validator\Exception\ApiSchemaException;
use CHStudio\Raven\Validator\Exception\DataSchemaException;
use CHStudio\Raven\Validator\Exception\GenericException;
use Exception;
use InvalidArgumentException;
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

        static::assertNull(
            (new ValidationExceptionMapper())->map($exceptionChain)
        );
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

        static::assertInstanceOf(DataSchemaException::class, $mapped);
        static::assertSame('a.b', $mapped->path);
        static::assertSame($error, $mapped->getPrevious());
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

        static::assertInstanceOf(ApiSchemaException::class, $mapped);
        static::assertSame($error, $mapped->getPrevious());
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

        static::assertInstanceOf(GenericException::class, $mapped);
    }

    public function testItMapsInvalidQueryArgs(): void
    {
        $invalidQueryArgsError = InvalidQueryArgs::becauseOfMissingRequiredArgument(
            'argument',
            $this->createMock(OperationAddress::class),
            RequiredParameterMissing::fromName('argument')
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

        static::assertInstanceOf(GenericException::class, $mapped);
        static::assertSame($invalidQueryArgsError, $mapped->getPrevious());
    }
}
