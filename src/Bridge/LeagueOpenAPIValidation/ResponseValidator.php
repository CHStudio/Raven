<?php

namespace CHStudio\Raven\Bridge\LeagueOpenAPIValidation;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use CHStudio\Raven\Validator\Exception\ResponseNotExpectedException;
use CHStudio\Raven\Validator\ResponseValidatorInterface;
use League\OpenAPIValidation\PSR7\Exception\NoResponseCode;
use League\OpenAPIValidation\PSR7\PathFinder;
use League\OpenAPIValidation\PSR7\ResponseValidator as LeagueResponseValidator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseValidator implements ResponseValidatorInterface
{
    public function __construct(
        private readonly LeagueResponseValidator $adapted,
        private readonly ValidationExceptionMapper $mapper
    ) {
    }

    public function validate(ResponseInterface $input, RequestInterface $request): void
    {
        try {
            $pathFinder = new PathFinder(
                $this->adapted->getSchema(),
                $request->getUri(),
                $request->getMethod()
            );

            foreach ($pathFinder->search() as $operation) {
                $this->adapted->validate($operation, $input);
            }
        } catch (NoResponseCode $e) {
            throw new ResponseNotExpectedException($request, $input, $e);
        } catch (\Throwable $e) {
            // Capture league/openapi-psr7-validator SpecFinder errors
            // it reads properties that might not exists.
            if (str_contains($e->getFile(), 'SpecFinder.php')) {
                throw new ResponseNotExpectedException($request, $input, $e);
            }

            $error = $this->mapper->map($e);
            if ($error !== null) {
                throw $error;
            }
            throw $e;
        }
    }
}
