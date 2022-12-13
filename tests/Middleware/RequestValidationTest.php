<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Illuminate\Http\Request as IlluminateRequest;
use Membrane\Laravel\Http\Response as MembraneResponse;
use Membrane\Result\Result;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Laravel\Middleware\RequestValidation
 * @uses   \Membrane\Laravel\Http\Request
 * @uses   \Membrane\Laravel\Http\Response
 * @uses   \Membrane\Laravel\ToPsr7
 */
class RequestValidationTest extends TestCase
{
    /** @test */
    public function handleTest(): void
    {
        $expected = new MembraneResponse(
            result: Result::valid([
            'path' => [],
            'query' => [],
            'header' => [],
            'cookie' => [],
            'body' => '',
        ])
        );
        $api = __DIR__ . '/../fixtures/petstore-expanded.json';
        $sut = new RequestValidation($api);
        $request = IlluminateRequest::create('/pets');

        $actual = $sut->handle($request, fn($var) => new MembraneResponse(status: 200, result: $var->getResult()));

        self::assertEquals($expected, $actual);
    }

}
