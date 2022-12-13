<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Closure;
use Crell\ApiProblem\ApiProblem;
use Crell\ApiProblem\HttpConverter;
use Membrane\Laravel\Http\Request as MembraneHttpRequest;
use Membrane\Laravel\ToSymfony;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResponseJsonNested
{
    public function handle(MembraneHttpRequest $request, Closure $next): SymfonyResponse
    {
        $result = $request->getResult();

        if (!$result->isValid()) {
            $renderer = new JsonNested($result);

            $problem = (new ApiProblem('Request payload failed validation'))
                ->setStatus(400);
            $problem['errors'] = $renderer->jsonSerialize();

            $factory = new Psr17Factory();
            $converter = new HttpConverter($factory);
            $response = $converter->toJsonResponse($problem);
            $toSymfony = new ToSymfony();

            return $toSymfony($response);
        }

        return $next($request);
    }
}
