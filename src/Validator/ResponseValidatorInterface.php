<?php

namespace CHStudio\Raven\Validator;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseValidatorInterface
{
    public function validate(ResponseInterface $response, RequestInterface $request): void;
}
