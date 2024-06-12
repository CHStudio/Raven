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

        self::assertInstanceOf(ExpectationCollection::class, $collection);
        $expectations = iterator_to_array($collection);
        self::assertCount(1, $expectations);
        self::assertInstanceOf(StatusCode::class, $expectations[0]);
        self::assertSame(201, $expectations[0]->statusCode);
    }

    public function testItReturnsAnEmptyCollectionWhenNoExpectationsCanBeBuilt(): void
    {
        $collection = (new ExpectationFactory())
            ->fromArray(['a' => true, 'b' => 'c']);

        self::assertInstanceOf(ExpectationCollection::class, $collection);
        $expectations = iterator_to_array($collection);
        self::assertCount(0, $expectations);
    }
}
