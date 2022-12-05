<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Membrane\Membrane;
use Membrane\OpenAPI\Method;
use Membrane\Result\Result;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class RequestValidation
{
    public function __construct(
        private readonly string $apiSpecPath
    ) {
    }

    public function handle(Request $request, Closure $next): Closure|Response
    {
        $method = Method::tryFrom(strtolower($request->getMethod())) ?? throw new Exception('not supported');

        $psr17Factory = new Psr17Factory();
        $httpMessageFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psr7Request = $httpMessageFactory->createRequest($request);

        $specification = new \Membrane\OpenAPI\Specification\Request(
            $this->apiSpecPath,
            $psr7Request->getUri()->getPath(),
            $method
        );

        $membrane = new Membrane();
        $result = $membrane->process($psr7Request, $specification);

        if (!$result->isValid()) {
            return new Response('', 400);
        }

        return $next($request->merge([Result::class => $result]));
    }
}
