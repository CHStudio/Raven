<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory;

use CHStudio\Raven\Http\Factory\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stringable;

final class UriTest extends TestCase
{
    public function testItCanBeBuiltFromString(): void
    {
        $uri = new Uri('a string');
        static::assertInstanceOf(Stringable::class, $uri);
        static::assertSame('a string', $uri->__toString());
    }

    public function testItFailsToBuildFromArrayWithoutBase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Uri(['parameters' => []]);
    }

    /**
     * @dataProvider giveArrays
     *
     */
    public function testItCanBeBuiltFromArray($array, $expected): void
    {
        $uri = new Uri($array);
        static::assertSame($expected, $uri->__toString());
    }

    public function giveArrays(): \Generator
    {
        yield [
            [
                'base' => 'http://param.host.int?value=key',
                'parameters' => [
                    'param' => 'value',
                    'int' => 0
            ]], 'http://value.host.0?value=key'
        ];

        yield [
            [
                'base' => 'http://chstudio.fr?{name}={value}',
                'parameters' => [
                    '{name}' => 'keyword',
                    '{value}' => '%s'
            ]], 'http://chstudio.fr?keyword=%s'
        ];

        yield [
            [
                'base' => 'https://chstudio.fr'
            ], 'https://chstudio.fr'
        ];
    }

    /**
     * @dataProvider giveInvalidValues
     *
     */
    public function testItCantBeBuiltFromOtherValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Uri(0);
    }

    public function giveInvalidValues(): \Generator
    {
        yield [0];
        yield [[]];
        yield [['parameters' => []]];
        yield [null];
    }
}
