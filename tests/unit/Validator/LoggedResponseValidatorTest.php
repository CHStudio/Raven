<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator;

use CHStudio\Raven\Validator\LoggedResponseValidator;
use CHStudio\Raven\Validator\ResponseValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class LoggedResponseValidatorTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $decorated = $this->createMock(ResponseValidatorInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $factory = new LoggedResponseValidator($logger, $decorated);

        self::assertInstanceOf(ResponseValidatorInterface::class, $factory);
    }

    public function testItLogsRequestAtDebugLevel(): void
    {
        $decorated = $this->createMock(ResponseValidatorInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $response
            ->expects(self::exactly(2))
            ->method('getStatusCode')
            ->willReturn(408);
        $response
            ->expects(self::exactly(2))
            ->method('getReasonPhrase')
            ->willReturn('I\'m not a teapost');

        $logger
            ->expects(self::exactly(2))
            ->method('debug')
            ->withConsecutive(
                ['Start testing Response: [408] I\'m not a teapost'],
                ['Finish testing Response: [408] I\'m not a teapost']
            );

        $decorated
            ->expects(self::once())
            ->method('validate')
            ->with($response, $request);

        (new LoggedResponseValidator($logger, $decorated))->validate($response, $request);
    }
}
