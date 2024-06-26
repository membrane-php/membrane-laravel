<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Membrane\Builder\Builder;
use Membrane\Laravel\Middleware\RequestValidation;
use Membrane\Membrane;
use Membrane\OpenAPIRouter\Router;
use Membrane\OpenAPIRouter\RouteCollection;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private const CONFIG_PATH = __DIR__ . '/../config/membrane.php';

    private const CONFIG_NAME = 'membrane';

    public function boot(): void
    {
        $this->publishes(
            [self::CONFIG_PATH => config_path('membrane.php')],
            [self::CONFIG_NAME]
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                Command\CacheOpenAPIRoutes::class,
                Command\CacheOpenAPIProcessors::class,
            ]);
        }
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
        $this->app->when(ApiProblemBuilder::class)
            ->needs('$apiProblemTypes')
            ->giveConfig('membrane.api_problem_response_types');

        $this->app->when(Membrane::class)
            ->needs('$builders')
            ->give($this->instantiateBuilders());
    }

    /** @return Builder[] */
    private function instantiateBuilders(): array
    {
        if (
            !file_exists(config('membrane.routes_file')) ||
            empty(config('membrane.additional_builders'))
        ) {
            return [];
        }

        $router = new Router(
            new RouteCollection(include config('membrane.routes_file'))
        );

        return array_filter(
            array_map(
                fn($className) => new $className($router),
                config('membrane.additional_builders')
            ),
            fn($class) => $class instanceof Builder,
        );
    }
}
