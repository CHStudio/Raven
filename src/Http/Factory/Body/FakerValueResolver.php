<?php

namespace CHStudio\Raven\Http\Factory\Body;

use Faker\Generator;
use InvalidArgumentException;
use JsonException;

class FakerValueResolver implements BodyResolverInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly BodyResolverInterface $resolver
    ) {
    }

    public function resolve(mixed $value): mixed
    {
        if (\is_string($value) && preg_match('/<([^(]+)\((.*)\)>/', $value, $matches) === 1) {
            return $this->resolveFakerValue($matches);
        }

        return $this->resolver->resolve($value);
    }

    /**
     * @param array<int, string> $matches
     */
    private function resolveFakerValue(array $matches): mixed
    {
        try {
            $methodName = $matches[1];
            $arguments =  json_decode('['.$matches[2].']', true, 512, JSON_THROW_ON_ERROR);

            return $this->faker->__call(
                $methodName,
                \is_array($arguments) ? $arguments : [$arguments]
            );
        } catch (InvalidArgumentException) {
        } catch (JsonException $error) {
            $message = sprintf(
                "Can't extract the arguments to call method %s: [%s]",
                $matches[1],
                $matches[2]
            );
            throw new InvalidArgumentException($message, 0, $error);
        }

        return null;
    }
}
