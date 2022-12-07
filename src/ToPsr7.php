<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Illuminate\Http\Request;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class ToPsr7
{
    private readonly PsrHttpFactory $httpMessageFactory;

    public function __construct()
    {
        $psr17Factory = new Psr17Factory();
        $this->httpMessageFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
    }

    public function __invoke(Request $request): ServerRequestInterface
    {
        return $this->httpMessageFactory->createRequest($request);
    }
}
