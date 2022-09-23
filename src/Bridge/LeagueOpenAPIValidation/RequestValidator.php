<?php

namespace CHStudio\Raven\Bridge\LeagueOpenAPIValidation;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use CHStudio\Raven\Validator\Exception\OperationNotFoundException;
use CHStudio\Raven\Validator\RequestValidatorInterface;
use League\OpenAPIValidation\PSR7\Exception\MultipleOperationsMismatchForRequest;
use League\OpenAPIValidation\PSR7\Exception\NoOperation;
use League\OpenAPIValidation\PSR7\Exception\NoPath;
use League\OpenAPIValidation\PSR7\RequestValidator as LeagueRequestValidator;
use Psr\Http\Message\RequestInterface;

class RequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly LeagueRequestValidator $adapted,
        private readonly ValidationExceptionMapper $mapper
    ) {
    }

    public function validate(RequestInterface $input): void
    {
        try {
            $this->adapted->validate($input);
        } catch (NoOperation|NoPath|MultipleOperationsMismatchForRequest $e) {
            throw new OperationNotFoundException($input, $e);
        } catch (\Throwable $e) {
            $error = $this->mapper->map($e);
            if ($error !== null) {
                throw $error;
            }
            throw $e;
        }
    }
}
