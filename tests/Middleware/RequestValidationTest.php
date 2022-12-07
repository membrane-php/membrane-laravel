<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Illuminate\Http\Request as IlluminateRequest;
use Membrane\Laravel\Http\Response as MembraneResponse;
use Membrane\Result\FieldName;
use Membrane\Result\Message;
use Membrane\Result\MessageSet;
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
    public function dataSetsToHandle(): array
    {
        $api = __DIR__ . '/../fixtures/petstore-expanded.json';
        return [
            [
                $api,
                IlluminateRequest::create('/pets'),
                new MembraneResponse(result: Result::valid(1)),
            ],
            [
                $api,
                IlluminateRequest::create('/pets?tags[]=Ben'),
                new MembraneResponse(result: Result::valid(1)),
            ],
            [
                $api,
                IlluminateRequest::create('/pets?tags=Ben'),
                new MembraneResponse(
                    status: 400,
                    result: Result::invalid(
                        [
                            'path' => [],
                            'query' => ['tags' => 'Ben'],
                            'header' => [],
                            'cookie' => [],
                            'body' => '',
                        ],
                        new MessageSet(
                            new FieldName('', '', 'query', 'tags'),
                            new Message('IsList validator expects list value, %s passed instead', ['string'])
                        )
                    )
                ),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataSetsToHandle
     */
    public function handleTest(string $api, IlluminateRequest $request, MembraneResponse $expected): void
    {
        $sut = new RequestValidation($api);

        $actual = $sut->handle($request, fn($var) => new MembraneResponse(status: 200, result: $expected->result));

        self::assertEquals($expected, $actual);
    }

}
