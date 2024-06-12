<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory;

use CHStudio\Raven\Http\Factory\Headers;
use CHStudio\Raven\Http\Factory\RequestFactory;
use CHStudio\Raven\Http\Factory\RequestFactoryInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface as PsrRequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class RequestFactoryTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $requestFactory = $this->createMock(PsrRequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        $factory = new RequestFactory($requestFactory, $streamFactory);

        self::assertInstanceOf(RequestFactoryInterface::class, $factory);
    }

    public function testItThrowsExceptionWhenUriIsNotPresent(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $requestFactory = $this->createMock(PsrRequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        (new RequestFactory($requestFactory, $streamFactory))
            ->fromArray([]);
    }

    public function testItBuildRequestFromArray(): void
    {
        $body = ['a' => 'b', 'c' => true];
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer token'
        ];

        $requestFactory = $this->createMock(PsrRequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $bodyStream = $this->createMock(StreamInterface::class);

        $requestFactory
            ->expects(self::once())
            ->method('createRequest')
            ->with('POST', 'https://chstudio.fr')
            ->willReturn($request);

        $request
            ->expects(self::exactly(2))
            ->method('withHeader')
            ->withConsecutive(... $this->denormalizeHeaders($headers))
            ->willReturn($request);
        $request
            ->expects(self::once())
            ->method('withBody')
            ->with($bodyStream)
            ->willReturn($request);

        $streamFactory
            ->expects(self::once())
            ->method('createStream')
            ->with(json_encode($body))
            ->willReturn($bodyStream);

        $request = (new RequestFactory($requestFactory, $streamFactory))
            ->fromArray([
                'uri' => 'https://chstudio.fr',
                'headers' => $headers,
                'method' => 'POST',
                'body' => $body
            ]);

        self::assertInstanceOf(RequestInterface::class, $request);
    }

    public function testItBuildRequestFromArrayWithoutBody(): void
    {
        $requestFactory = $this->createMock(PsrRequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $request = $this->createMock(RequestInterface::class);

        $requestFactory
            ->expects(self::once())
            ->method('createRequest')
            ->with('GET', 'https://chstudio.fr')
            ->willReturn($request);

        $request->expects(self::never())->method('withHeader');
        $request->expects(self::never())->method('withBody');

        $streamFactory->expects(self::never())->method('createStream');

        $request = (new RequestFactory($requestFactory, $streamFactory))
            ->fromArray([
                'uri' => 'https://chstudio.fr'
            ]);

        self::assertInstanceOf(RequestInterface::class, $request);
    }

    /**
     *
     * @dataProvider provideItBuildRequestFromArrayWithFormDataBodyCases
     */
    public function testItBuildRequestFromArrayWithFormDataBody($body, $headers, $bodyAsString): void
    {
        $requestFactory = $this->createMock(PsrRequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $bodyStream = $this->createMock(StreamInterface::class);

        $requestFactory
            ->expects(self::once())
            ->method('createRequest')
            ->with('POST', 'https://chstudio.fr')
            ->willReturn($request);

        if ((is_countable($headers) ? \count($headers) : 0) > 0) {
            $request
                ->expects(self::exactly(1))
                ->method('withHeader')
                ->withConsecutive(... $this->denormalizeHeaders($headers))
                ->willReturn($request);
        } else {
            $request
                ->expects(self::never())
                ->method('withHeader');
        }
        $request
            ->expects(self::once())
            ->method('withBody')
            ->with($bodyStream)
            ->willReturn($request);

        $streamFactory
            ->expects(self::once())
            ->method('createStream')
            ->with($bodyAsString)
            ->willReturn($bodyStream);

        $request = (new RequestFactory($requestFactory, $streamFactory))
            ->fromArray([
                'uri' => 'https://chstudio.fr',
                'headers' => $headers,
                'method' => 'POST',
                'body' => $body
            ]);

        self::assertInstanceOf(RequestInterface::class, $request);
    }

    public static function provideItBuildRequestFromArrayWithFormDataBodyCases(): iterable
    {
        $body = ['a' => 'b', 'c' => true];

        yield 'JSON content type' => [
            $body,
            ['Content-Type' => 'application/json'],
            json_encode($body)
        ];
        yield 'No content type means JSON encoding' => [
            $body,
            [],
            json_encode($body)
        ];
        yield 'FormData content type' => [
            $body,
            ['Content-Type' => 'multipart/form-data'],
            http_build_query($body)
        ];
        yield 'Input body as string' => [
            '["a", "b", "c"]',
            ['Content-Type' => 'multipart/form-data'],
            '["a", "b", "c"]'
        ];
    }

    private function denormalizeHeaders(array $input): array
    {
        $denormalized = [];

        foreach (new Headers($input) as $name => $value) {
            $denormalized[] = [$name, $value];
        }

        return $denormalized;
    }
}
