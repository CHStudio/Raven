<?php

namespace CHStudio\Raven\Validator\Expectation;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, ResponseExpectationInterface>
 */
class ExpectationCollection implements IteratorAggregate
{
    /**
     * @var ResponseExpectationInterface[]
     */
    private readonly array $expectations;

    public function __construct(
        ResponseExpectationInterface ...$expectations
    ) {
        $this->expectations = $expectations;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->expectations);
    }
}
