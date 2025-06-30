<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Membrane\Laravel\ApiProblemBuilder;
use Membrane\Renderer\JsonNested;
use Membrane\Result\Result;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResponseJsonNested
{
    public function __construct(
        private Container $container,
        private ApiProblemBuilder $apiProblemBuilder
    ) {
    }

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $result = $this->container->get(Result::class);

        /**
         * Laravel 12 adds typehints that make this assert unnecessary
         * Earlier versions of laravel require this assertion
         * @TODO Remove assertion when support dropped for Laravel 11 or earlier.
         *
         * @phpstan-ignore-next-line
         */
        assert($result instanceof Result);

        if (!$result->isValid()) {
            return $this->apiProblemBuilder->buildFromRenderer(new JsonNested($result));
        }

        return $next($request);
    }
}
