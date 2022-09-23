<?php

namespace CHStudio\Raven;

use CHStudio\Raven\Validator\Exception\ValidationException;
use CHStudio\Raven\Validator\Expectation\ExpectationCollection;
use CHStudio\Raven\Validator\Expectation\ExpectationFailedException;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class LoggedErrorExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly ExecutorInterface $decorated,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(RequestInterface $request, ExpectationCollection $expectations): void
    {
        try {
            $this->decorated->execute($request, $expectations);
        } catch (ValidationException|ExpectationFailedException $error) {
            $this->logger->emergency(
                $error->getMessage(),
                ['error' => $error]
            );
        }
    }
}
