<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Membrane\Laravel\Http\Request as MembraneHttpRequest;
use Membrane\Laravel\ToPsr7;
use Membrane\Membrane;
use Membrane\OpenAPI\Specification\Request as MembraneRequestSpec;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RequestValidation
{
    private readonly Membrane $membrane;
    private readonly ToPsr7 $toPsr7;

    public function __construct(
        private readonly string $apiSpecPath
    ) {
        $this->membrane = new Membrane();
        $this->toPsr7 = new ToPsr7();
    }

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $psr7Request = ($this->toPsr7)($request);

        $specification = MembraneRequestSpec::fromPsr7($this->apiSpecPath, $psr7Request);

        $result = $this->membrane->process($psr7Request, $specification);

        $membraneRequest = MembraneHttpRequest::createFromResult($result, $request);

        return $next($membraneRequest);
    }
}
