<?php

namespace CHStudio\Raven\Http\Factory\Body;

interface BodyResolverInterface
{
    public function resolve(mixed $value): mixed;
}
