<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory;

use CHStudio\Raven\Http\Factory\Resolver\ValueResolverInterface;
use CHStudio\Raven\Http\Factory\RequestUriParametersResolver;
use CHStudio\Raven\Http\Factory\RequestFactoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class RequestUriParametersResolverTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $bodyResolver = $this->createMock(ValueResolverInterface::class);
        $decorated = $this->createMock(RequestFactoryInterface::class);

        $factory = new RequestUriParametersResolver($bodyResolver, $decorated);

        static::assertInstanceOf(RequestFactoryInterface::class, $factory);
    }

    public function testItResolvesUriParametersWhenThereIsOne(): void
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
            ->with(['uri' => ['parameters' => ['c']]])
            ->willReturn($this->createMock(RequestInterface::class));

        (new RequestUriParametersResolver($bodyResolver, $decorated))
            ->fromArray(['uri' => ['parameters' => ['a' => 'b']]]);
    }

    public function testItDoesntCallResolverOnEmptyParameters(): void
    {
        $bodyResolver = $this->createMock(ValueResolverInterface::class);
        $decorated = $this->createMock(RequestFactoryInterface::class);

        $bodyResolver->expects(static::never())->method('resolve');

        $decorated
            ->expects(static::once())
            ->method('fromArray')
            ->with([])
            ->willReturn($this->createMock(RequestInterface::class));

        (new RequestUriParametersResolver($bodyResolver, $decorated))
            ->fromArray([]);
    }
}
