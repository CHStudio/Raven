<?php

namespace CHStudio\Raven\Validator\Exception;

use RuntimeException;
use Throwable;

class GenericException extends RuntimeException implements ValidationException
{
    public function __construct(
        Throwable $previous
    ) {
        $message = sprintf(
            'Something went wrong: %s.',
            $previous->getMessage()
        );

        parent::__construct($message, 0, $previous);
    }
}
