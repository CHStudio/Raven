<?php

namespace CHStudio\Raven\Validator\Exception;

use RuntimeException;
use Throwable;

class DataSchemaException extends RuntimeException implements ValidationException
{
    public function __construct(
        public readonly mixed $value,
        public readonly string $path,
        Throwable $previous
    ) {
        $message = sprintf(
            "Data validation failed for property %s, invalid value: %s\n%s",
            $path,
            var_export($value, true),
            $previous->getMessage()
        );

        parent::__construct($message, 0, $previous);
    }
}
