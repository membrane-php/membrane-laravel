<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Crell\ApiProblem\ApiProblem;
use Crell\ApiProblem\HttpConverter;
use Membrane\Renderer\Renderer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

class ApiProblemBuilder
{
    public function build(Renderer $renderer): ResponseInterface
    {
        $problem = (new ApiProblem('Request payload failed validation'))->setStatus(400);
        $problem['errors'] = $renderer->jsonSerialize();

        $converter = new HttpConverter(new Psr17Factory());

        return $converter->toJsonResponse($problem);
    }
}
