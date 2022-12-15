<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Membrane\Laravel\Middleware\RequestValidation;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        /** @phpstan-ignore-next-line */ // config_path is a laravel framework helper method
        $this->publishes([__DIR__ . '/../config/membrane.php' => config_path('membrane.php')], ['membrane']);
    }

    public function register(): void
    {
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
