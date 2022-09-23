<?php

namespace CHStudio\Raven\Validator\Exception;

use RuntimeException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ResponseNotExpectedException extends RuntimeException implements ValidationException
{
    public function __construct(
        public readonly RequestInterface $request,
        public readonly ResponseInterface $response,
        Throwable $previous
    ) {
        $message = sprintf(
            'API response with status code %d isn\'t defined in the spec for request [%s] %s.',
            $response->getStatusCode(),
            $request->getMethod(),
            $request->getUri()
        );

        parent::__construct($message, 0, $previous);
    }
}
