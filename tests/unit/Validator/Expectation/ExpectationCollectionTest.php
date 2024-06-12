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

        self::assertInstanceOf(IteratorAggregate::class, $collection);
        $iterator = $collection->getIterator();

        self::assertSame($expectation, $iterator->current());
    }
}
