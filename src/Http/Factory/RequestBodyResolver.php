<?php

namespace CHStudio\Raven\Http\Factory;

use CHStudio\Raven\Http\Factory\Resolver\ValueResolverInterface;
use Psr\Http\Message\RequestInterface;

class RequestBodyResolver implements RequestFactoryInterface
{
    public function __construct(
        private readonly ValueResolverInterface $resolver,
        private readonly RequestFactoryInterface $decorated
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function fromArray(array $data): RequestInterface
    {
        if (isset($data['body']) && \is_array($data['body'])) {
            $data['body'] = $this->resolver->resolve($data['body']);
        }

        return $this->decorated->fromArray($data);
    }
}
