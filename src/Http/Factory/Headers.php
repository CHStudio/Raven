<?php

declare(strict_types=1);

namespace CHStudio\Raven\Http\Factory;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use Stringable;
use Traversable;

/**
 * @implements IteratorAggregate<string, array<string>>
 */
class Headers implements IteratorAggregate, Stringable
{
    /**
     * @var array<string, array<string>>
     */
    private array $headers = [];

    public function __construct(mixed $headers = [])
    {
        if (!\is_array($headers)) {
            throw new InvalidArgumentException('headers collection must be an array.');
        }

        foreach ($headers as $name => $values) {
            if (!\is_string($name)) {
                throw new InvalidArgumentException('Header name be a string.');
            }

            if (\is_string($values)) {
                $this->append($name, $values);
            } elseif (\is_array($values)) {
                foreach ($values as $value) {
                    if (!\is_string($value)) {
                        throw new InvalidArgumentException('Header values must be string[]|string.');
                    }
                    $this->append($name, $value);
                }
            } else {
                throw new InvalidArgumentException('Header values must be string[]|string.');
            }
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->headers);
    }

    public function __toString(): string
    {
        $asString = '';
        foreach ($this->headers as $name => $values) {
            $name = ucwords($name, '-');
            foreach ($values as $value) {
                $asString .= \sprintf('%s: %s', $name, $value).PHP_EOL;
            }
        }

        return $asString;
    }

    private function normalizeName(string $name): string
    {
        return strtolower($name);
    }

    public function has(string $offset): bool
    {
        return isset($this->headers[$this->normalizeName($offset)]);
    }

    public function first(string $offset): ?string
    {
        $values = $this->get($offset);
        return \count($values) > 0 ? current($values) : null;
    }

    /**
     * @return string[]
     */
    public function get(string $offset): array
    {
        return $this->headers[$this->normalizeName($offset)] ?? [];
    }

    public function append(string $offset, string $value): void
    {
        $normalized = $this->normalizeName($offset);

        if (!isset($this->headers[$normalized])) {
            $this->headers[$normalized] = [$value];
        } else {
            $this->headers[$normalized][] = $value;
        }
    }
}
