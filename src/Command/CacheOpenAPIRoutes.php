<?php

declare(strict_types=1);

namespace Membrane\Laravel\Command;

use Illuminate\Console\Command;
use Membrane\OpenAPIRouter;
use Symfony\Component\Console\Logger\ConsoleLogger;

final class CacheOpenAPIRoutes extends Command
{
    protected $signature = 'membrane:cache-openapi-routes' .
    '{openapi : Absolute filepath of OpenAPI}' .
    '{--destination= : Cache directory}';

    protected $description = <<<TEXT
        Cache routes specified in OpenAPI file.

        You may specify a --destination
        Defaults to "<CWD>/cache/"
        TEXT;

    public function handle(): int
    {
        $openapi = $this->argument('openapi');

        if (!is_string($openapi)) {
            $this->error('openapi filepath MUST be a string');
            return Command::FAILURE;
        }

        $destination = $this->option('destination') ??
            getcwd() . '/cache';

        if (!is_string($destination)) {
            $this->error('destination MUST be a string');
            return Command::FAILURE;
        }

        $service = new OpenAPIRouter\Console\Service\CacheOpenAPIRoutes(
            new ConsoleLogger($this->output)
        );

        return $service->cache(
            $openapi,
            rtrim($destination, '/') . '/routes.php',
        ) ?
            Command::SUCCESS :
            Command::FAILURE;
    }
}
