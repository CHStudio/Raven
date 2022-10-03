<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory\Body;

use CHStudio\Raven\Http\Factory\Body\ArrayValueResolver;
use CHStudio\Raven\Http\Factory\Body\BodyResolverInterface;
use PHPUnit\Framework\TestCase;

final class ArrayValueResolverTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $decorated = $this->createMock(BodyResolverInterface::class);
        $arrayResolver = new ArrayValueResolver($decorated);

        static::assertInstanceOf(BodyResolverInterface::class, $arrayResolver);
    }

    public function testItResolveEachInnerArrayRecursively(): void
    {
        $value = [
            'a' => 'b',
            'c' => [
                'd' => 'e',
                'f' => [
                    'g' => 'h'
                ]
            ]
        ];

        $decorated = $this->createMock(BodyResolverInterface::class);
        $decorated
            ->expects(static::exactly(3))
            ->method('resolve')
            ->withConsecutive(
                ['b'],
                ['e'],
                ['h'],
            )
            ->willReturn('updated');

        $arrayResolver = new ArrayValueResolver($decorated);

        static::assertSame(
            [
                'a' => 'updated',
                'c' => [
                    'd' => 'updated',
                    'f' => [
                        'g' => 'updated'
                    ]
                ]
            ],
            $arrayResolver->resolve($value)
        );
    }
}
