<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory\Resolver;

use CHStudio\Raven\Http\Factory\Resolver\ValueResolverInterface;
use CHStudio\Raven\Http\Factory\Resolver\PassThroughValueResolver;
use PHPUnit\Framework\TestCase;

final class PassThroughValueResolverTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $resolver = new PassThroughValueResolver();

        self::assertInstanceOf(ValueResolverInterface::class, $resolver);
    }

    public function testItReturnGivenValue(): void
    {
        $resolver = new PassThroughValueResolver();

        self::assertNull($resolver->resolve(null));
        self::assertSame(0, $resolver->resolve(0));
        self::assertTrue($resolver->resolve(true));
        self::assertSame([], $resolver->resolve([]));
    }
}
