<?php

namespace CHStudio\Raven\Http\Factory;

use CHStudio\Raven\Http\Factory\RequestFactoryInterface as InternalRequestFactoryInterface;
use Psr\Http\Message\RequestFactoryInterface;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class RequestFactory implements InternalRequestFactoryInterface
{
    public function __construct(
        public readonly RequestFactoryInterface $requestFactory,
        public readonly StreamFactoryInterface $streamFactory
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function fromArray(array $data): RequestInterface
    {
        if (!isset($data['uri'])) {
            throw new InvalidArgumentException('"uri" key must be defined foreach Request.');
        }

        $uri = new Uri($data['uri']);
        $headers = new Headers($data['headers'] ?? []);
        $method = \is_string($data['method']) ? $data['method'] : 'GET';

        $request = $this->requestFactory->createRequest($method, (string) $uri);
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if (isset($data['body'])) {
            if (\is_string($data['body'])) {
                $body = $data['body'];
            } elseif (\is_array($data['body'])) {
                $body = match ($headers->first('Content-Type')) {
                    'application/json' => json_encode($data['body']),
                    'multipart/form-data' => http_build_query($data['body']),
                    default => json_encode($data['body'])
                };
            }

            if (isset($body)) {
                $request = $request->withBody(
                    $this->streamFactory->createStream((string) $body)
                );
            }
        }

        return $request;
    }
}
