<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Expectation\ExpectationCollection;
use CHStudio\Raven\Validator\Expectation\ResponseExpectationInterface;
use IteratorAggregate;
use PHPUnit\Framework\TestCase;

final class ExpectationCollectionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $expectation = $this->createMock(ResponseExpectationInterface::class);
        $collection = new ExpectationCollection($expectation);

        static::assertInstanceOf(IteratorAggregate::class, $collection);
        $iterator = $collection->getIterator();

        static::assertSame($expectation, $iterator->current());
    }
}
