<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Membrane\Laravel\ApiProblemBuilder;
use Membrane\Renderer\JsonFlat;
use Membrane\Result\Result;
use Psr\Http\Message\ResponseInterface;

class ResponseJsonFlat
{
    public function __construct(
        private Container $container
    ) {
    }

    public function handle(Request $request, Closure $next): Response|ResponseInterface
    {
        $result = $this->container->get(Result::class);

        assert($result instanceof Result);
        if (!$result->isValid()) {
            return (new ApiProblemBuilder())->build(new JsonFlat($result));
        }

        return $next($request);
    }
}
