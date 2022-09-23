<?php

namespace CHStudio\Raven\Http\Factory;

use InvalidArgumentException;
use Stringable;

class Uri implements Stringable
{
    private readonly string $uri;

    public function __construct(mixed $value)
    {
        if (\is_array($value)) {
            if (!isset($value['base'])) {
                throw new InvalidArgumentException(
                    'If you want to build an URI from an array, use the schema: [ base => string, ?parameters => [string => mixed]'
                );
            }

            $parameters = $value['parameters'] ?? [];
            $value = str_replace(array_keys($parameters), $parameters, $value['base']);
        } elseif (!\is_string($value)) {
            throw new InvalidArgumentException('$value must be a string or an array.');
        }
        $this->uri = $value;
    }

    public function __toString(): string
    {
        return $this->uri;
    }
}
