<?php

namespace CHStudio\Raven\Bridge\LeagueOpenAPIValidation;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;

class Factory
{
    private ValidationExceptionMapper $mapper;

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
        return new RequestValidator($this->validator->getRequestValidator(), $this->mapper);
    }

    public function getResponseValidator(): ResponseValidator
    {
        return new ResponseValidator($this->validator->getResponseValidator(), $this->mapper);
    }
}
