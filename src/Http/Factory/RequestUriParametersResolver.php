<?php

namespace CHStudio\Raven\Http\Factory;

use CHStudio\Raven\Http\Factory\Resolver\ValueResolverInterface;
use Psr\Http\Message\RequestInterface;

class RequestUriParametersResolver implements RequestFactoryInterface
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
        if (isset($data['uri']) && \is_array($data['uri'])) {
            $data['uri']['parameters'] = $this->resolver->resolve($data['uri']['parameters'] ?? []);
        }

        return $this->decorated->fromArray($data);
    }
}
