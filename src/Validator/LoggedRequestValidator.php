<?php

namespace CHStudio\Raven\Validator;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class LoggedRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RequestValidatorInterface $decorated
    ) {
    }

    public function validate(RequestInterface $request): void
    {
        $this->logger->debug(sprintf(
            "Start testing Request: [%s] %s",
            $request->getMethod(),
            $request->getUri()
        ));
        $this->decorated->validate($request);
        $this->logger->debug(sprintf(
            'Finish testing Request: [%s] %s',
            $request->getMethod(),
            $request->getUri()
        ));
    }
}
