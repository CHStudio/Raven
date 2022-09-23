<?php

namespace CHStudio\Raven;

use CHStudio\Raven\Validator\Expectation\ExpectationCollection;
use Psr\Http\Message\RequestInterface;

interface ExecutorInterface
{
    public function execute(RequestInterface $request, ExpectationCollection $expectations): void;
}
