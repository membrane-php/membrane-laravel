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

### Global Usage

To validate requests for all your routes, add the `RequestValidation` middleware to `$middleware` property
of  `app/Http/Kernel.php` class:

```php
protected $middleware = [
  \Membrane\Laravel\RequestValidation::class,
    // ...
];
```

**Please note:** It is not required to be at the top of your middleware,
but it must precede anything that relies on receiving a valid request.
