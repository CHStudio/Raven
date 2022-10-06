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

        static::assertInstanceOf(ValueResolverInterface::class, $resolver);
    }

    public function testItReturnGivenValue(): void
    {
        $resolver = new PassThroughValueResolver();

        static::assertNull($resolver->resolve(null));
        static::assertSame(0, $resolver->resolve(0));
        static::assertTrue($resolver->resolve(true));
        static::assertSame([], $resolver->resolve([]));
    }
}
