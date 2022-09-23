<?php

namespace CHStudio\Raven\Validator\Expectation;

class ExpectationFactory
{
    /**
     * @param array<string|int, string|array<string>> $data
     */
    public function fromArray(array $data): ExpectationCollection
    {
        $expectations = [];

        if (isset($data['statusCode'])) {
            $expectations[] = new StatusCode((int) $data['statusCode']);
        }

        return new ExpectationCollection(... $expectations);
    }
}
