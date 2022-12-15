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
        private Container $container
    ) {
    }

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $result = $this->container->get(Result::class);

        assert($result instanceof Result);
        if (!$result->isValid()) {
            return (new ApiProblemBuilder())->build(new JsonNested($result));
        }

        return $next($request);
    }
}
