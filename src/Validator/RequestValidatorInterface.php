<?php

namespace CHStudio\Raven\Validator;

use Psr\Http\Message\RequestInterface;

interface RequestValidatorInterface
{
    public function validate(RequestInterface $request): void;
}
