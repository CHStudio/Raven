<?php

namespace CHStudio\Raven\Validator\Exception;

use League\OpenAPIValidation\PSR7\Exception\Validation\RequiredParameterMissing;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

class RequiredParameterMissingException extends RuntimeException implements ValidationException
{
    public function __construct(
        RequiredParameterMissing $previous,
    ) {
        $message = \sprintf('A required parameter is missing: "%s"', $previous->name());

        parent::__construct($message, 0, $previous);
    }
}
