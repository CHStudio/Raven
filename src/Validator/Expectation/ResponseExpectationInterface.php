<?php

declare(strict_types=1);

namespace CHStudio\Raven\Validator\Expectation;

use Psr\Http\Message\ResponseInterface;

interface ResponseExpectationInterface
{
    public function verify(ResponseInterface $message): ?ExpectationFailedException;
}
