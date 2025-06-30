# Membrane-Laravel

Integrates [Membrane-core](https://github.com/membrane-php/membrane-core) with [Laravel](https://laravel.com/).

## About

Middleware that validates the raw user input from incoming HTTP requests against your OpenAPI spec.  
Adds a `Membrane\Result\Result` onto your `Illuminate\Contracts\Container\Container`.  
The Result object contains the cleaned up data and additional details in the case of invalid requests.

## Setup

### Installation

Require the `membrane/laravel` package in your composer.json and update your dependencies:

```shell
composer require membrane/laravel
```

### Configuration

The defaults are set in `config/membrane.php`.  
To publish a copy to your own config, use the following:

```shell
php artisan vendor:publish --tag="membrane"
```

#### API Spec File

This is the **absolute** filepath of your OpenAPI.

By default, it looks for `<your-project-directory>/api/openapi.yaml`.

#### Validation Error Response Code

Set `'validation_error_response_code'` to the **integer** value of the default http status code for invalid results.

#### Validation Error Response Type

Set `'validation_error_response_type'` to the **string** value of the default response type for API problems.

#### API Problem Response Types

Within the `'api_problem_response_types'` array:
Set **integer** http status code => **string** response type pairs.  
These are more specific and will override the default value set by `'validation_error_response_type'`

## Usage

### Requests

The `\Membrane\Laravel\Middleware\RequestValidation` middleware will validate or invalidate incoming requests and let
you decide
how to react.
You can follow it with your own custom middleware or with one of the following built-in options to produce an error
response:

### Responses

Any response middleware MUST follow the `RequestValidation` middleware as it requires the `result` object being added to
your container.  
These middlewares will check whether the request has passed or failed validation.  
Invalid requests will return an appropriate response detailing the reasons the request was invalid.

Your response can be in one of the following formats.

#### Flat Json

`\Membrane\Laravel\Middleware\ResponseJsonFlat`

```json
{
    "errors":{
        "pet->id":["must be an integer"],
        "pet":["name is a required field"]
    },
    "title":"Request payload failed validation",
    "type":"about:blank",
    "status":400
}
```

#### Nested Json

`\Membrane\Laravel\Middleware\ResponseJsonNested`

```json
{
    "errors":{
        "errors":[],
        "fields":{
            "pet":{
                "errors":[
                    "name is a required field"
                ],
                "fields":{
                    "id":{
                        "errors":[
                            "must be an integer"
                        ],
                        "fields":[]
                    }
                }
            }
        }
    },
    "title":"Request payload failed validation",
    "type":"about:blank",
    "status":400
}
```

### Global Usage

To use any of the above middlewares on all routes, go into your `app/Http/Kernel.php` and add them to your `middleware`
array.

For example:

```php
protected $middleware = [
  \Membrane\Laravel\Middleware\RequestValidation::class,
  \Membrane\Laravel\Middleware\ResponseJsonFlat::class
  // ...
];
```

In Laravel 11.x/12.x `app/HttpKernel.php` has been removed, global configuration goes into `bootstrap/app.php`:

For example:

```php
return Application::configure(basePath: dirname(__DIR__))
    // ...
    ->withMiddleware(function (Middleware $middleware) {
        // ...
        $middleware->appendToGroup('api', [
            \Membrane\Laravel\Middleware\RequestValidation::class,
            \Membrane\Laravel\Middleware\ResponseJsonFlat::class,
        ]);
    })
    // ...
    ->create();
```
