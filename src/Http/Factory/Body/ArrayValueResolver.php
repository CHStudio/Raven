<?php

namespace CHStudio\Raven\Http\Factory\Body;

class ArrayValueResolver implements BodyResolverInterface
{
    public function __construct(
        private readonly BodyResolverInterface $resolver
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
