<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Expectation;

use CHStudio\Raven\Validator\Expectation\ExpectationCollection;
use CHStudio\Raven\Validator\Expectation\ExpectationFactory;
use CHStudio\Raven\Validator\Expectation\StatusCode;
use PHPUnit\Framework\TestCase;

final class ExpectationFactoryTest extends TestCase
{
    public function testItBuildsExpectationFromArray(): void
    {
        $collection = (new ExpectationFactory())
            ->fromArray(['statusCode' => 201]);

        static::assertInstanceOf(ExpectationCollection::class, $collection);
        $expectations = iterator_to_array($collection);
        static::assertCount(1, $expectations);
        static::assertInstanceOf(StatusCode::class, $expectations[0]);
        static::assertSame(201, $expectations[0]->statusCode);
    }

    public function testItReturnsAnEmptyCollectionWhenNoExpectationsCanBeBuilt(): void
    {
        $collection = (new ExpectationFactory())
            ->fromArray(['a' => true, 'b' => 'c']);

        static::assertInstanceOf(ExpectationCollection::class, $collection);
        $expectations = iterator_to_array($collection);
        static::assertCount(0, $expectations);
    }
}
