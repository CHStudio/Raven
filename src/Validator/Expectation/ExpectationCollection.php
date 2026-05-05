<?php

declare(strict_types=1);

namespace CHStudio\Raven\Validator\Expectation;

use ArrayIterator;
use IteratorAggregate;

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

    /**
     * @return ArrayIterator<int|string, ResponseExpectationInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->expectations);
    }
}
