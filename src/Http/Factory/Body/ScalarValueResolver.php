<?php

namespace CHStudio\Raven\Http\Factory\Body;

class ScalarValueResolver implements BodyResolverInterface
{
    public function resolve(mixed $value): mixed
    {
        return $value;
    }
}
