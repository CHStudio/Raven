<?php

namespace CHStudio\Raven\Validator\Exception;

use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

class OperationNotFoundException extends RuntimeException implements ValidationException
{
    public function __construct(
        public readonly RequestInterface $request,
        Throwable $previous = null
    ) {
        $message = sprintf(
            'API operation for request [%s] %s hasn\'t been found.',
            $request->getMethod(),
            $request->getUri()
        );

        parent::__construct($message, 0, $previous);
    }
}
