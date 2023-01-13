<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Crell\ApiProblem\ApiProblem;
use Crell\ApiProblem\HttpConverter;
use Membrane\OpenAPI\Exception\CannotProcessRequest;
use Membrane\Renderer\Renderer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiProblemBuilder
{
    /** @param string[] $apiProblemTypes */
    public function __construct(
        private readonly int $errorCode,
        private readonly string $errorType,
        private readonly array $apiProblemTypes
    ) {
    }

    /** @deprecated */
    public function build(Renderer $renderer): SymfonyResponse
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        return $this->buildFromRenderer($renderer);
    }

    public function buildFromRenderer(Renderer $renderer): SymfonyResponse
    {
        $problem = (new ApiProblem('Request payload failed validation'))
            ->setStatus($this->errorCode)
            ->setType($this->errorType);
        $problem['errors'] = $renderer->jsonSerialize();

        return $this->convertToResponse($problem);
    }

    public function buildFromException(CannotProcessRequest $exception): SymfonyResponse
    {
        $errorCode = match ($exception->getCode()) {
            0 => 404,
            1 => 405,
            2 => 406,
            default => $this->errorCode
        };

        $problem = (new ApiProblem(SymfonyResponse::$statusTexts[$errorCode]))
            ->setStatus($errorCode)
            ->setType($this->apiProblemTypes[$errorCode] ?? $this->errorType)
            ->setDetail($exception->getMessage());

        return $this->convertToResponse($problem);
    }

    private function convertToResponse(ApiProblem $problem): SymfonyResponse
    {
        $converter = new HttpConverter(new Psr17Factory());

        return (new ToSymfony())($converter->toJsonResponse($problem));
    }
}
