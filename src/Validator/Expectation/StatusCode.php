<?php

namespace CHStudio\Raven\Validator\Expectation;

use Psr\Http\Message\ResponseInterface;

class StatusCode implements ResponseExpectationInterface
{
    public function __construct(
        public readonly int $statusCode
    ) {
    }

    public function verify(ResponseInterface $message): ?ExpectationFailedException
    {
        return $message->getStatusCode() === $this->statusCode
            ? null
            : new ExpectationFailedException(
                sprintf('Unexpected status code %s, expected %s', $message->getStatusCode(), $this->statusCode),
                $this
            );
    }
}
