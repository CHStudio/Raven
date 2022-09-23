<?php

declare(strict_types=1);

namespace CHStudio\RavenTest;

use CHStudio\Raven\ExecutorInterface;
use CHStudio\Raven\LoggedErrorExecutor;
use CHStudio\Raven\Validator\Exception\GenericException;
use CHStudio\Raven\Validator\Expectation\ExpectationCollection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

final class LoggedErrorExecutorTest extends TestCase
{
    public function testCanBeBuilt(): void
    {
        $executor = new LoggedErrorExecutor(
            $this->createMock(ExecutorInterface::class),
            $this->createMock(LoggerInterface::class)
        );

        static::assertInstanceOf(ExecutorInterface::class, $executor);
    }

    public function testWillLoggerWillNotBeCalledWithoutException(): void
    {
        $expectations = new ExpectationCollection();
        $executor = $this->createMock(ExecutorInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $request = $this->createMock(RequestInterface::class);

        $executor
            ->expects(static::once())
            ->method('execute')
            ->with($request, $expectations);
        $logger
            ->expects(static::never())
            ->method('emergency');

        (new LoggedErrorExecutor($executor, $logger))
            ->execute($request, $expectations);
    }

    public function testWillLogOnEncounteredException(): void
    {
        $expectations = new ExpectationCollection();
        $executor = $this->createMock(ExecutorInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $request = $this->createMock(RequestInterface::class);

        $error = new GenericException(new \Exception('message'));

        $executor
            ->expects(static::once())
            ->method('execute')
            ->with($request, $expectations)
            ->willThrowException($error);
        $logger
            ->expects(static::once())
            ->method('emergency')
            ->with(static::stringContains('message'), ['error' => $error]);

        (new LoggedErrorExecutor($executor, $logger))
            ->execute($request, $expectations);
    }
}
