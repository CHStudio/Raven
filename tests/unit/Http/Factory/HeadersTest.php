<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory;

use CHStudio\Raven\Http\Factory\Headers;
use InvalidArgumentException;
use IteratorAggregate;
use PHPUnit\Framework\TestCase;
use Stringable;

final class HeadersTest extends TestCase
{
    public function testItCanBeBuiltFromString(): void
    {
        $uri = new Headers([]);
        static::assertInstanceOf(Stringable::class, $uri);
        static::assertInstanceOf(IteratorAggregate::class, $uri);
    }

    public function testItFailsOnNonArrayParameter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Headers(false);
    }

    public function testItCanBeFilledAndChecked(): void
    {
        $headers = new Headers(['Content-Type' => 'application/json']);

        static::assertTrue($headers->has('Content-Type'));
        static::assertTrue($headers->has('content-type'));
        static::assertTrue($headers->has('content-Type'));

        $expected = ['application/json'];

        static::assertSame($expected, $headers->get('Content-Type'));
        static::assertSame($expected, $headers->get('content-type'));
        static::assertSame($expected, $headers->get('content-Type'));
        static::assertSame('application/json', $headers->first('content-Type'));

        static::assertSame([], $headers->get('Non-Existing-Header'));

        $headers->set('content-type', 'other/type');

        static::assertSame(['application/json', 'other/type'], $headers->get('Content-Type'));

        $iterator = iterator_to_array($headers);

        static::assertArrayHasKey('content-type', $iterator);
        static::assertSame(['application/json', 'other/type'], $iterator['content-type']);
    }

    public function testItCanBeSerializedToString(): void
    {
        $headers = new Headers([
            'Content-Type' => 'application/json',
            'A' => ['B', 'C']
        ]);

        static::assertSame(
            <<<STRING
        Content-Type: application/json
        A: B
        A: C

        STRING,
            (string) $headers
        );
    }
}
