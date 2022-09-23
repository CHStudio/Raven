<?php

namespace CHStudio\Raven\Validator;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class LoggedResponseValidator implements ResponseValidatorInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ResponseValidatorInterface $decorated
    ) {
    }

    public function validate(ResponseInterface $response, RequestInterface $request): void
    {
        $this->logger->debug(sprintf(
            'Start testing Response: [%d] %s',
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));

        $this->decorated->validate($response, $request);

        $this->logger->debug(sprintf(
            'Finish testing Response: [%d] %s',
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));
    }
}
