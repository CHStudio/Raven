<?php

namespace CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception;

use CHStudio\Raven\Validator\Exception\ApiSchemaException;
use CHStudio\Raven\Validator\Exception\DataSchemaException;
use CHStudio\Raven\Validator\Exception\GenericException;
use CHStudio\Raven\Validator\Exception\RequiredParameterMissingException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidQueryArgs;
use League\OpenAPIValidation\PSR7\Exception\Validation\RequiredParameterMissing;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\Schema\BreadCrumb;
use League\OpenAPIValidation\Schema\Exception\InvalidSchema;
use League\OpenAPIValidation\Schema\Exception\SchemaMismatch;
use Throwable;

class ValidationExceptionMapper
{
    public function map(Throwable $error): ?ValidationException
    {
        $chain = [];
        $current = $error;
        while ($current) {
            $chain[] = $current;
            $current = $current->getPrevious();
        }

        $lastError = end($chain);

        if ($lastError instanceof SchemaMismatch) {
            return $this->mapSchemaMismatch($lastError);
        } elseif ($lastError instanceof InvalidSchema) {
            return new ApiSchemaException($lastError);
        } elseif ($lastError instanceof RequiredParameterMissing) {
            return new RequiredParameterMissingException($lastError);
        } elseif ($lastError instanceof ValidationFailed) {
            return $this->mapGeneric($chain, $lastError);
        }

        return null;
    }

    /**
     * @param array<Throwable> $chain
     */
    private function mapGeneric(array $chain, ValidationFailed $lastError): GenericException
    {
        $previousError = prev($chain);
        return new GenericException($previousError instanceof InvalidQueryArgs ? $previousError : $lastError);
    }

    private function mapSchemaMismatch(SchemaMismatch $lastError): DataSchemaException
    {
        $breadCrumb = $lastError->dataBreadCrumb();
        if ($breadCrumb instanceof BreadCrumb) {
            $crumbs = array_reduce(
                $breadCrumb->buildChain(),
                static function (string $carry, mixed $value): string {
                    $strValue = \is_int($value) || \is_string($value) ? (string) $value : '';

                    return $carry !== '' ? $carry . '.' . $strValue : $strValue;
                },
                ''
            );
        }

        return new DataSchemaException($lastError->data(), $crumbs ?? '', $lastError);
    }
}
