<?php

namespace CHStudio\Raven\Validator\Expectation;

use Psr\Http\Message\ResponseInterface;

interface ResponseExpectationInterface
{
    public function verify(ResponseInterface $message): ?ExpectationFailedException;
}
