<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory;

use CHStudio\Raven\Http\Factory\Resolver\ValueResolverInterface;
use CHStudio\Raven\Http\Factory\RequestBodyResolver;
use CHStudio\Raven\Http\Factory\RequestFactoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class RequestBodyResolverTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $bodyResolver = $this->createMock(ValueResolverInterface::class);
        $decorated = $this->createMock(RequestFactoryInterface::class);

        $factory = new RequestBodyResolver($bodyResolver, $decorated);

        static::assertInstanceOf(RequestFactoryInterface::class, $factory);
    }

    public function testItResolvesBodyWhenThereIsOne(): void
    {
        $bodyResolver = $this->createMock(ValueResolverInterface::class);
        $decorated = $this->createMock(RequestFactoryInterface::class);

        $bodyResolver
            ->expects(static::once())
            ->method('resolve')
            ->with(['a' => 'b'])
            ->willReturn(['c']);

        $decorated
            ->expects(static::once())
            ->method('fromArray')
            ->with(['body' => ['c']])
            ->willReturn($this->createMock(RequestInterface::class));

        (new RequestBodyResolver($bodyResolver, $decorated))
            ->fromArray(['body' => ['a' => 'b']]);
    }

    public function testItDoesntCallResolverOnEmptyBody(): void
    {
        $bodyResolver = $this->createMock(ValueResolverInterface::class);
        $decorated = $this->createMock(RequestFactoryInterface::class);

        $bodyResolver->expects(static::never())->method('resolve');

        $decorated
            ->expects(static::once())
            ->method('fromArray')
            ->with([])
            ->willReturn($this->createMock(RequestInterface::class));

        (new RequestBodyResolver($bodyResolver, $decorated))
            ->fromArray([]);
    }
}
