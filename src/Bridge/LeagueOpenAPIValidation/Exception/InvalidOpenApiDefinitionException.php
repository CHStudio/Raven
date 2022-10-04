<?php

namespace CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Exception;

use InvalidArgumentException;
use Throwable;

class InvalidOpenApiDefinitionException extends InvalidArgumentException
{
    public function __construct(Throwable $previous)
    {
        parent::__construct(sprintf(
            "The given OpenApi definition can't be loaded:\n%s -> %s",
            $previous::class,
            $previous->getMessage()
        ), 0, $previous);
    }
}
