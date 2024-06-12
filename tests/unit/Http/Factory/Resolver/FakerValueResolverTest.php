<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory\Resolver;

use CHStudio\Raven\Http\Factory\Resolver\ValueResolverInterface;
use CHStudio\Raven\Http\Factory\Resolver\FakerValueResolver;
use Faker\Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FakerValueResolverTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $resolver = new FakerValueResolver(
            $this->createMock(Generator::class),
            $this->createMock(ValueResolverInterface::class)
        );

        self::assertInstanceOf(ValueResolverInterface::class, $resolver);
    }

    /**
     * @dataProvider provideItPassTheValueToNextResolverInDifferentCases
     */
    public function testItPassTheValueToNextResolverInDifferent(mixed $parameter): void
    {
        $decorated = $this->createMock(ValueResolverInterface::class);
        $decorated
            ->expects(self::once())
            ->method('resolve')
            ->with($parameter)
            ->willReturn($parameter);

        $resolver = new FakerValueResolver(
            $this->createMock(Generator::class),
            $decorated
        );

        $resolver->resolve($parameter);
    }

    public static function provideItPassTheValueToNextResolverInDifferentCases(): iterable
    {
        yield [null];
        yield [true];
        yield [[]];
        yield ['abcdef'];
        yield ['<abdr()'];
        yield ['abdr()>'];
    }

    /**
     * @dataProvider provideItResolveTheValueThroughFakerGeneratorCases
     */
    public function testItResolveTheValueThroughFakerGenerator(string $parameter, string $method, array $arguments): void
    {
        $decorated = $this->createMock(ValueResolverInterface::class);
        $decorated
            ->expects(self::never())
            ->method('resolve');
        $generator = $this->createStub(Generator::class);
        $generator
            ->expects(self::once())
            ->method('__call')
            ->with($method, $arguments)
            ->willReturn('generatedValue');

        $resolver = new FakerValueResolver(
            $generator,
            $decorated
        );

        self::assertSame('generatedValue', $resolver->resolve($parameter));
    }

    public static function provideItResolveTheValueThroughFakerGeneratorCases(): iterable
    {
        yield ['<method()>', 'method', []];
        yield ['<more_complexMethod(true, false, 0)>', 'more_complexMethod', [true, false, 0]];
        yield ['<date("2018")>', 'date', ["2018"]];
    }

    public function testItCaptureFakerInvalidArguments(): void
    {
        $decorated = $this->createMock(ValueResolverInterface::class);
        $decorated
            ->expects(self::never())
            ->method('resolve');
        $generator = $this->createStub(Generator::class);
        $generator
            ->expects(self::once())
            ->method('__call')
            ->with('method', [])
            ->willThrowException(new \InvalidArgumentException('Error'));

        $resolver = new FakerValueResolver(
            $generator,
            $decorated
        );

        self::assertNull($resolver->resolve('<method()>'));
    }

    public function testItFailsOnFunctionArgumentThatCantBeJsonDecoded(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Can\'t extract the arguments to call method/');

        $decorated = $this->createMock(ValueResolverInterface::class);
        $decorated
            ->expects(self::never())
            ->method('resolve');
        $generator = $this->createStub(Generator::class);
        $generator
            ->expects(self::never())
            ->method('__call');

        $resolver = new FakerValueResolver(
            $generator,
            $decorated
        );

        self::assertNull($resolver->resolve('<method(abc")>'));
    }
}
