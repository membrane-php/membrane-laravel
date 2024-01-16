<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Membrane\Laravel\ApiProblemBuilder;
use Membrane\Laravel\ToPsr7;
use Membrane\Membrane;
use Membrane\OpenAPI\Exception\CannotProcessRequest;
use Membrane\OpenAPI\Exception\CannotProcessSpecification;
use Membrane\OpenAPI\Specification\Request as MembraneRequestSpec;
use Membrane\Result\Result;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RequestValidation
{
    private readonly Membrane $membrane;
    private readonly ToPsr7 $toPsr7;

    public function __construct(
        private readonly string $apiSpecPath,
        private readonly ApiProblemBuilder $apiProblemBuilder,
        private Container $container
    ) {
        $this->membrane = new Membrane();
        $this->toPsr7 = new ToPsr7();
    }

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $psr7Request = ($this->toPsr7)($request);

        try {
            $specification = MembraneRequestSpec::fromPsr7($this->apiSpecPath, $psr7Request);
            $result = $this->membrane->process($psr7Request, $specification);
        } catch (CannotProcessSpecification $e) {
            return $this->apiProblemBuilder->buildFromException($e);
        }

        $this->container->instance(Result::class, $result);

        return $next($request);
    }
}
