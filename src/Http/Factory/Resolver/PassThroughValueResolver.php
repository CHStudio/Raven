<?php

namespace CHStudio\Raven\Http\Factory\Resolver;

class PassThroughValueResolver implements ValueResolverInterface
{
    public function resolve(mixed $value): mixed
    {
        return $value;
    }
}
