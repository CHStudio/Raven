<?php

namespace CHStudio\Raven\Http\Factory\Body;

use Faker\Generator;
use InvalidArgumentException;

class FakerValueResolver implements BodyResolverInterface
{
    public function __construct(
        public readonly Generator $faker,
        private readonly BodyResolverInterface $resolver
    ) {
    }

    public function resolve(mixed $value): mixed
    {
        if (\is_string($value) && preg_match('/<([^(]+)\((.*)\)>/', $value, $matches) === 1) {
            return $this->resolveFakerValue($matches) ?? $value;
        }

        return $this->resolver->resolve($value);
    }

    /**
     * @param array<int, string> $matches
     */
    private function resolveFakerValue(array $matches): mixed
    {
        $methodName = $matches[1];
        $arguments =  json_decode('['.$matches[2].']', true, 512, JSON_THROW_ON_ERROR);

        try {
            /** @var callable(): mixed */
            $callable = [$this->faker, $methodName];

            return \call_user_func_array(
                $callable,
                \is_array($arguments) ? $arguments : [$arguments]
            );
        } catch (InvalidArgumentException $e) {
        }

        return null;
    }
}
