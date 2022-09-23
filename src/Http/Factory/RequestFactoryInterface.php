<?php

namespace CHStudio\Raven\Http\Factory;

use Psr\Http\Message\RequestInterface;

interface RequestFactoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function fromArray(array $data): RequestInterface;
}
