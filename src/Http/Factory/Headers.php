<?php

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
            $this->set($name, $values);
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
                $asString .= sprintf('%s: %s', $name, $value).PHP_EOL;
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
     * @return array<string|null>
     */
    public function get(string $offset): array
    {
        return $this->headers[$this->normalizeName($offset)] ?? [];
    }

    /**
     * @param array<string>|string $values
     */
    public function set(string $offset, array|string $values): void
    {
        $normalized = $this->normalizeName($offset);

        if (\is_array($values)) {
            $values = array_values($values);

            if (!isset($this->headers[$normalized])) {
                $this->headers[$normalized] = $values;
            } else {
                $this->headers[$normalized] = array_merge($this->headers[$normalized], $values);
            }
        } else {
            if (!isset($this->headers[$normalized])) {
                $this->headers[$normalized] = [$values];
            } else {
                $this->headers[$normalized][] = $values;
            }
        }
    }
}
