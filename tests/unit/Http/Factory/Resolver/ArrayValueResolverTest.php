<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory\Resolver;

use CHStudio\Raven\Http\Factory\Resolver\ArrayValueResolver;
use CHStudio\Raven\Http\Factory\Resolver\ValueResolverInterface;
use PHPUnit\Framework\TestCase;

final class ArrayValueResolverTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $decorated = $this->createMock(ValueResolverInterface::class);
        $arrayResolver = new ArrayValueResolver($decorated);

        self::assertInstanceOf(ValueResolverInterface::class, $arrayResolver);
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

        $decorated = $this->createMock(ValueResolverInterface::class);
        $decorated
            ->expects(self::exactly(3))
            ->method('resolve')
            ->withConsecutive(
                ['b'],
                ['e'],
                ['h'],
            )
            ->willReturn('updated');

        $arrayResolver = new ArrayValueResolver($decorated);

        self::assertSame(
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
