<?php

declare(strict_types=1);

namespace Membrane\Laravel\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Logger\ConsoleLogger;

final class CacheOpenAPIProcessors extends Command
{
    protected $signature = 'membrane:cache-openapi-routes' .
    '{openapi : Absolute filepath of OpenAPI}' .
    '{--destination= : Cache directory}' .
    '{--namespace=Membrane\Cache}' .
    '{--skip-requests}' .
    '{--skip-responses}';

    protected $description = <<<TEXT
        Cache routes specified in OpenAPI file.

        You may specify the cache --destination
        Defaults to "<CWD>/cache/"

        You may specify the cache --namespace
        Defaults to "Membrane\Cache"

        You may avoid caching requests with --skip-requests
        You may avoid caching responses with --skip-responses
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

        $namespace = $this->option('namespace');
        if (!is_string($namespace)) {
            $this->error('namespace MUST be a string');
            return Command::FAILURE;
        }

        $buildRequests = !$this->option('skip-requests');
        assert(is_bool($buildRequests));
        $buildResponses = !$this->option('skip-responses');
        assert(is_bool($buildResponses));

        $service = new \Membrane\Console\Service\CacheOpenAPIProcessors(
            new ConsoleLogger($this->output)
        );

        return $service->cache(
            $openapi,
            $destination,
            $namespace,
            $buildRequests,
            $buildResponses,
        ) ?
            Command::SUCCESS :
            Command::FAILURE;
    }
}
