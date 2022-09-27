<?php

namespace CHStudio\Raven\Http\Factory\Body;

class PassThroughValueResolver implements BodyResolverInterface
{
    public function resolve(mixed $value): mixed
    {
        return $value;
    }
}
