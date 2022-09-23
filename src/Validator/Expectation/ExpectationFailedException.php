<?php

namespace CHStudio\Raven\Validator\Expectation;

use RuntimeException;

class ExpectationFailedException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ResponseExpectationInterface $expectation
    ) {
        parent::__construct($message, 0);
    }
}
