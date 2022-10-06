<?php

namespace CHStudio\Raven\Http\Factory\Resolver;

interface ValueResolverInterface
{
    public function resolve(mixed $value): mixed;
}
