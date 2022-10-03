<?php

namespace CHStudio\Raven;

use CHStudio\Raven\Validator\Expectation\ExpectationCollection;
use CHStudio\Raven\Validator\RequestValidatorInterface;
use CHStudio\Raven\Validator\ResponseValidatorInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class Executor implements ExecutorInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestValidatorInterface $requestValidator,
        private readonly ResponseValidatorInterface $responseValidator
    ) {
    }

    public function execute(
        RequestInterface $request,
        ExpectationCollection $expectations = null
    ): void {
        $this->requestValidator->validate($request);
        $response = $this->client->sendRequest($request);

        if ($expectations !== null) {
            foreach ($expectations as $expectation) {
                $error = $expectation->verify($response);
                if ($error !== null) {
                    throw $error;
                }
            }
        }

        $this->responseValidator->validate($response, $request);
    }
}
