# Membrane-Laravel

Integrates [Membrane-core](https://github.com/membrane-php/membrane-core) with [Laravel](https://laravel.com/).

## About

Middleware that validates the raw user input from incoming HTTP requests against your OpenAPI spec.  
Extends the `Illuminate\Http\Request` and `Illuminate\Http\Response` with a `Membrane\Result\Result` object containing
the cleaned up data and additional details in the case of invalid requests.

## Setup

### Installation

Require the `membrane/laravel` package in your composer.json and update your dependencies:

```text
composer require membrane/laravel
```

### Configuration

The defaults are set in `config/membrane.php`.  
To publish a copy to your own config, use the following:

```text
php artisan vendor:publish --tag="membrane"
```

### Usage

#### Request Validation

The `RequestValidation` middleware will validate or invalidate incoming requests and let you decide how to react.
You can precede it with your own custom middleware or precede it with one of the following built-in options:

#### Nested Json Response

The `ResponseJsonNested` MUST precede the `RequestValidation` middleware
as it relies on the container containing the result.
It will check whether the request has passed or failed validation.
Invalid requests will return a response detailing the reasons the request was invalid.

#### Flat Json Response

The `ResponseJsonFlat` MUST precede the `RequestValidation` middleware 
as it relies on the container containing the result.
It will check whether the request has passed or failed validation.
Invalid requests will return a response detailing the reasons the request was invalid.

### Global Usage

```php
protected $middleware = [
  \Membrane\Laravel\RequestValidation::class,
    // ...
];
```
