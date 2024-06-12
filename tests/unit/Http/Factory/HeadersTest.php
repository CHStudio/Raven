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
        self::assertInstanceOf(Stringable::class, $uri);
        self::assertInstanceOf(IteratorAggregate::class, $uri);
    }

    public function testItFailsOnNonArrayParameter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Headers(false);
    }

    public function testItCanBeFilledAndChecked(): void
    {
        $headers = new Headers(['Content-Type' => 'application/json']);

        self::assertTrue($headers->has('Content-Type'));
        self::assertTrue($headers->has('content-type'));
        self::assertTrue($headers->has('content-Type'));

        $expected = ['application/json'];

        self::assertSame($expected, $headers->get('Content-Type'));
        self::assertSame($expected, $headers->get('content-type'));
        self::assertSame($expected, $headers->get('content-Type'));
        self::assertSame('application/json', $headers->first('content-Type'));

        self::assertSame([], $headers->get('Non-Existing-Header'));

        $headers->set('content-type', 'other/type');

        self::assertSame(['application/json', 'other/type'], $headers->get('Content-Type'));

        $iterator = iterator_to_array($headers);

        self::assertArrayHasKey('content-type', $iterator);
        self::assertSame(['application/json', 'other/type'], $iterator['content-type']);
    }

    public function testItCanBeSerializedToString(): void
    {
        $headers = new Headers([
            'Content-Type' => 'application/json',
            'A' => ['B', 'C']
        ]);

        self::assertSame(
            <<<STRING
        Content-Type: application/json
        A: B
        A: C

        STRING,
            (string) $headers
        );
    }
}
