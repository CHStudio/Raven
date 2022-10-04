# Raven - How to test your API documentation and behavior.

This library was written to allow testing OpenAPI documentation easily. It also
allows verifying that your code implementation is compatible with that
documentation.

## Why creating such tool ?

We work a lot on API related projects. Sometimes we create API, sometimes we
consume them. The OpenAPI specification format is now well known and used in a
lot of different contexts.

Our concern is that it's hard to ensure that the written documentation is
representing the current API behavior. We wanted to track the implementation
difference between the doc and the code.

We searched the ecosystem and found that it exists different tools to mock APIs
or to perform requests to them. However we can't find a tool that allows
performing HTTP requests that use fixtures and are able to perform specific
validation on the responses.

So we started working on `Raven`!

It relies on PSRs to be easily integrated in any project and is composed of two
different parts:

* An HTTP request factory to define the input,
* An executor that'll be responsible to actually validate Requests and
  Responses.

### Raven, isn't it a bird ?

Nope, we use here [the human name of the X-Men character Mystique](https://en.wikipedia.org/wiki/Mystique_(character)).
She's able to transform and adapt to any situation which is that tool goal. We
need to adapt to any API to trigger valid requests and analyze responses.

## Install it

Using Composer:

```bash
composer require chstudio/raven
```

To use Raven you might need to also install:

* [an HTTP message factory compatible with PSR-17](https://packagist.org/providers/psr/http-factory-implementation),
* [an HTTP client compatible with PSR-18](https://packagist.org/providers/psr/http-client-implementation).

Of course you can also write your own. The only constraint is to be compatible
with PSRs interfaces.

## Usage

### Execute Request / Response validation

This library defines its own interfaces for request validation. It comes with an
adapter to the [league/openapi-psr7-validator](https://packagist.org/packages/league/openapi-psr7-validator)
package which define a complete validation logic.

```php
<?php

use CHStudio\Raven\Bridge\LeagueOpenAPIValidation\Factory;
use CHStudio\Raven\Validator\Expectation\ExpectationCollection;

// Load OpenAPI specification
$factory = Factory::fromYamlFile('specific/path/to/openapi.yaml');

$executor = new Executor(
    /** Your own HTTP client implementation */,
    $factory->getRequestValidator(),
    $factory->getResponseValidator()
);

$executor->execute($request);
```

### Generate requests easily based on configuration

Writing `RequestInterface` objects manually might not be the simplest way to
define your test cases. We created a `RequestFactory` to help building those
objects. It rely on PSR17 HTTP factories.

Here is an example which use the [nyholm/psr7](https://packagist.org/packages/nyholm/psr7).

```php
<?php

use CHStudio\Raven\Http\Factory\RequestFactory;
use Nyholm\Psr7\Factory\Psr17Factory;

$psrFactory = new Psr17Factory();
$requestFactory = new RequestFactory($psrFactory, $psrFactory);

$request = $requestFactory->fromArray([
    'uri' => 'http://myhost.com/api/users/me',
    'method' => 'POST',
    'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer token'
    ],
    'body' => '{"aSimple": "objectDefinition", "yep": true}'
]);
```

If the body is given as an array, it will be encoded based on the `Content-Type`
header:

* `application/json`, no header or unsupported header will be transformed to
  JSON,
* `multipart/form-data`, will use `http_build_query`.

### Enrich your request body with resolver

Most of the time having static request bodies will not be powerful enough. We
need identifiers and other details extracted from our fixtures. A specific
layer can be added around the `RequestFactory` to resolve body dynamically.

You can combine different `Resolver` and let the configured body pass through
all the methods and be enriched. This library come with a specific `Faker`
resolver to generate data easily with providers (see [Faker doc](https://fakerphp.github.io/formatters/)).

You can build your own resolvers using the `BodyResolverInterface`.

```php
<?php

use CHStudio\Raven\Http\Factory\Body\ArrayValueResolver;
use CHStudio\Raven\Http\Factory\Body\FakerValueResolver;
use CHStudio\Raven\Http\Factory\Body\PassThroughValueResolver;

$generator = \Faker\Factory::create();

//Apply it on the request factory built in the previous section.
$requestFactory = new RequestBodyResolver(
    //Configure specific resolver logic.
    new ArrayValueResolver(
        new FakerValueResolver(
            $generator,
            new PassThroughValueResolver()
        )
    ),
    $requestFactory
);

$request = $requestFactory->fromArray([
    'uri' => 'http://myhost.com/api/users/me',
    'method' => 'POST',
    'body' => [
        'scalar' => [
            'bool' => true,
            'int' => 23456,
            'float' => 18.06
        ],
        //Built in Faker provider
        'faker' => [
            'name' => '<name()>',
            'creationDate' => '<date("Y-m-d")>',
        ]
        //Specific provider to query database
        'institution' => '<institutionId("Massachusetts General Hospital")>'
    ]
]);

/**
 * This will generate the following body:
 *
 * {
 *     "scalar": {
 *         "bool": true,
 *         "int": 23456,
 *         "float": 18.06
 *     },
 *     "faker": {
 *         "name": "John Doe",
 *         "creationDate": "2022-10-03"
 *     },
 *     "institution": "bf91c434-dcf3-3a4c-b49a-12e0944ef1e2"
 * }
 */
```

### Custom expectations

Validating that the request and the response are respecting the documentation is
nice but we might need to add some user defined expectations. Will this request
trigger a 401 response ? Is the body containing the correct value ?

Expectation can be built using request definition data. Based on some
properties, they will be added dynamically. The expectation collection can be
passed to the `Executor::execute` method.

If one of the expectation fails, the response validation will fail and you'll
get the details through a `ExpectationFailedException` error.

```php
<?php

use CHStudio\Raven\Validator\Expectation\ExpectationFactory;

$requestData = [
    'uri' => 'http://myhost.com/api/users/me',
    'method' => 'GET',
    'statusCode' => 403
];

$expectations = (new ExpectationFactory())->fromArray($requestData);
$request = $requestFactory->fromArray($requestData);

$executor->execute($request, $expectations);
```

This library come with built in expectations: `StatusCode`. You can easily build
your own using the `ResponseExpectationInterface`.

## License

This package is released under the [Apache-2 license](LICENCE).

## Contribute

If you wish to contribute to the project, please read the [CONTRIBUTING](CONTRIBUTING.md) notes.
