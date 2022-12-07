<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Membrane\Laravel\Middleware\RequestValidation;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            /** @phpstan-ignore-next-line */ // Cannot
            __DIR__ . '/../config/membrane.php' => config_path('membrane.php'),
        ]);
    }

    public function register(): void
    {
        $this->app->when(RequestValidation::class)
            ->needs('$apiSpecPath')
            ->giveConfig('membrane.api_spec_file');
    }
}
