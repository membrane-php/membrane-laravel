<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Membrane\Laravel\Middleware\RequestValidation;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private const CONFIG_PATH = __DIR__ . '/../config/membrane.php';
    private const CONFIG_NAME = 'membrane';

    public function boot(): void
    {
        /** @phpstan-ignore-next-line */ // config_path is a laravel framework helper method
        $this->publishes([self::CONFIG_PATH => config_path('membrane.php')], [self::CONFIG_NAME]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, self::CONFIG_NAME);

        $this->app->when(RequestValidation::class)
            ->needs('$apiSpecPath')
            ->giveConfig('membrane.api_spec_file');

        $this->app->when(ApiProblemBuilder::class)
            ->needs('$errorCode')
            ->giveConfig('membrane.validation_error_response_code');
        $this->app->when(ApiProblemBuilder::class)
            ->needs('$errorType')
            ->giveConfig('membrane.validation_error_response_type');
    }
}
