<?php

namespace CHStudio\Raven\Http\Factory\Resolver;

class ArrayValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ValueResolverInterface $resolver
    ) {
    }

    public function resolve(mixed $value): mixed
    {
        if (\is_array($value)) {
            foreach ($value as $name => $innerValue) {
                $value[$name] = $this->resolve($innerValue);
            }
            return $value;
        }

        return $this->resolver->resolve($value);
    }
}
