<?php

declare(strict_types=1);

namespace CHStudio\RavenTest;

use CHStudio\Raven\Executor;
use CHStudio\Raven\ExecutorInterface;
use CHStudio\Raven\Validator\Expectation\ExpectationCollection;
use CHStudio\Raven\Validator\Expectation\ExpectationFailedException;
use CHStudio\Raven\Validator\Expectation\ResponseExpectationInterface;
use CHStudio\Raven\Validator\RequestValidatorInterface;
use CHStudio\Raven\Validator\ResponseValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ExecutorTest extends TestCase
{
    public function testCanBeBuilt(): void
    {
        $executor = new Executor(
            $this->createMock(ClientInterface::class),
            $this->createMock(RequestValidatorInterface::class),
            $this->createMock(ResponseValidatorInterface::class),
        );

        self::assertInstanceOf(ExecutorInterface::class, $executor);
    }

    public function testCanExecuteARequest(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $requestValidator = $this->createMock(RequestValidatorInterface::class);
        $responseValidator = $this->createMock(ResponseValidatorInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $expectation = $this->createMock(ResponseExpectationInterface::class);

        $httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $requestValidator
            ->expects(self::once())
            ->method('validate')
            ->with($request);
        $responseValidator
            ->expects(self::once())
            ->method('validate')
            ->with($response);

        $expectation
            ->expects(self::once())
            ->method('verify')
            ->with($response)
            ->willReturn(null);

        (new Executor($httpClient, $requestValidator, $responseValidator))
            ->execute($request, new ExpectationCollection($expectation));
    }

    public function testBreaksIfExpectationFails(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $httpClient = $this->createMock(ClientInterface::class);
        $requestValidator = $this->createMock(RequestValidatorInterface::class);
        $responseValidator = $this->createMock(ResponseValidatorInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $expectation = $this->createMock(ResponseExpectationInterface::class);

        $httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $requestValidator
            ->expects(self::once())
            ->method('validate')
            ->with($request);
        $responseValidator
            ->expects(self::never())
            ->method('validate');

        $expectation
            ->expects(self::once())
            ->method('verify')
            ->with($response)
            ->willReturn(new ExpectationFailedException('Error', $expectation));

        (new Executor($httpClient, $requestValidator, $responseValidator))
            ->execute($request, new ExpectationCollection($expectation));
    }
}
