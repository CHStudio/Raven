<?php

declare(strict_types=1);

namespace CHStudio\Raven\Http\Factory\Resolver;

interface ValueResolverInterface
{
    public function resolve(mixed $value): mixed;
}
