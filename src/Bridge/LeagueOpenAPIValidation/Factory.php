<?php

namespace CHStudio\Raven\Bridge\LeagueOpenAPIValidation;

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\InvalidOpenApiDefinitionException;
use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception\ValidationExceptionMapper;
use InvalidArgumentException;
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
        if (!is_readable($path)) {
            throw new InvalidArgumentException(
                sprintf('Filename given isn\'t readable: %s', $path)
            );
        }

        return new self(
            (new ValidatorBuilder())->fromYamlFile($path)
        );
    }

    public static function fromJsonFile(string $path): self
    {
        if (!is_readable($path)) {
            throw new InvalidArgumentException(
                sprintf('Filename given isn\'t readable: %s', $path)
            );
        }

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
    
    /**
     * OpenApi doc is internally transposed by Raven to a tree of infos.
     * This method gives the opportinity to retreive a subtree, based on the "path" in the api doc
     *  that is represented by the tupple: http method / uri / status code / content type
     * One usage can be to further process the resulting array
     *  in order to check an api json response object's structure conformity
     */
    public function getReferenceStructure(string $method, string $uri, int $statusCode, ?string $contentType): ?array
    {
        $decoded = json_decode(json_encode(
            $this->validator->getResponseValidator()->getSchema()->getSerializableData()
        ), true);
        // Looking for paths with care of parameters not necessarily named the same in both yaml files (test def & openApi)
        $workedUri = $this->cleanPath($uri);
        foreach ($decoded['paths'] as $path => $data) {
            if ($path === $uri || $this->cleanPath($path) === $workedUri) {
                // Path to access data in multiple levels nested object
                $pathSegments = $this->buildPathSegments($method, $statusCode, $contentType);
                $roadTraveled = '';
                foreach ($pathSegments as $segment) {
                    $roadTraveled .= '/'.$segment;
                    if (!isset($data[$segment])) {
                        throw new InvalidArgumentException(sprintf(
                            'The following path was not found in object definition from openApi: %s',
                            $roadTraveled
                        ));
                    }
                    $data = $data[$segment];
                }

                return $data;
            }
        }

        return null;
    }

    private function cleanPath(string $path): string
    {
        // Replace parameter by a star and remove query string
        return preg_replace(['/\{[^\}]+\}/', '/\?.+/'], ['*', ''], $path);
    }

    private function buildPathSegments(string $method, int $statusCode, ?string $contentType): array
    {
        $return = [strtolower($method), 'responses', $statusCode, 'content'];
        if ($contentType !== null) {
            $return[] = strtolower($contentType);
        }
        $return[] = 'schema';
        $return[] = 'properties';

        return $return;
    }
}
