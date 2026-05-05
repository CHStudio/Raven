<?php

declare(strict_types=1);

namespace CHStudio\Raven\Http\Factory;

use InvalidArgumentException;
use Stringable;

class Uri implements Stringable
{
    private readonly string $uri;

    public function __construct(mixed $value)
    {
        if (\is_array($value)) {
            if (!isset($value['base']) || !\is_string($value['base'])) {
                throw new InvalidArgumentException(
                    'If you want to build an URI from an array, use the schema: [ base => string, ?parameters => [string => mixed]'
                );
            }

            // Check if parameters is a valid string[]
            $parameters = $value['parameters'] ?? [];
            if (!\is_array($parameters)) {
                throw new InvalidArgumentException('value parameters must be an array.');
            }

            $search = [];
            $replace = [];
            foreach ($parameters as $name => $parameter) {
                if (!\is_string($name) || !\is_scalar($parameter)) {
                    throw new InvalidArgumentException(\sprintf(
                        'Invalid parameter given {name: %s, parameter: %s}',
                        var_export($name, true),
                        var_export($parameter, true)
                    ));
                }
                $search[] = $name;
                $replace[] = (string) $parameter;
            }

            $this->uri = str_replace(
                $search,
                $replace,
                (string) $value['base']
            );
        } elseif (\is_string($value)) {
            $this->uri = $value;
        } else {
            throw new InvalidArgumentException('$value must be a string or an array.');
        }
    }

    public function __toString(): string
    {
        return $this->uri;
    }
}
