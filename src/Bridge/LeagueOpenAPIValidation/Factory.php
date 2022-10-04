<?php

namespace CHStudio\Raven\Bridge\LeagueOpenAPIValidation;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\InvalidOpenApiDefinitionException;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Throwable;

class Factory
{
    private readonly ValidationExceptionMapper $mapper;

    public function __construct(
        private readonly ValidatorBuilder $validator,
        ValidationExceptionMapper $mapper = null
    ) {
        $this->mapper = $mapper ?? new ValidationExceptionMapper();
    }

    public static function fromYamlFile(string $path): self
    {
        return new self(
            (new ValidatorBuilder())->fromYamlFile($path)
        );
    }

    public static function fromJsonFile(string $path): self
    {
        return new self(
            (new ValidatorBuilder())->fromJsonFile($path)
        );
    }

    public function getRequestValidator(): RequestValidator
    {
        try {
            return new RequestValidator($this->validator->getRequestValidator(), $this->mapper);
        } catch (Throwable $error) {
            throw new InvalidOpenApiDefinitionException($error);
        }
    }

    public function getResponseValidator(): ResponseValidator
    {
        try {
            return new ResponseValidator($this->validator->getResponseValidator(), $this->mapper);
        } catch (Throwable $error) {
            throw new InvalidOpenApiDefinitionException($error);
        }
    }
}
