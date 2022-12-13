<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ToSymfony
{
    private readonly HttpFoundationFactory $httpMessageFactory;

    public function __construct()
    {
        $this->httpMessageFactory = new HttpFoundationFactory();
    }

    public function __invoke(ResponseInterface $response): SymfonyResponse
    {
        return $this->httpMessageFactory->createResponse($response);
    }
}
