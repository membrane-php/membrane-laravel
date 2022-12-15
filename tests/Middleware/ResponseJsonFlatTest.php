<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Membrane\Result\FieldName;
use Membrane\Result\Message;
use Membrane\Result\MessageSet;
use Membrane\Result\Result;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Membrane\Laravel\Middleware\ResponseJsonFlat
 */
class ResponseJsonFlatTest extends TestCase
{
    public function dataSetsToHandle(): array
    {
        return [
            'valid results return valid responses' => [
                Result::valid(1),
                new Response(),
            ],
            'invalid result returns response with ApiProblem' => [
                Result::invalid(1, new MessageSet(new FieldName('id'), new Message('error message', []))),
                new \Nyholm\Psr7\Response(
                    status: 400,
                    headers: ['Content-Type' => ['application/problem+json']],
                    body: '{"errors":{"id":["error message"]}',
                ),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataSetsToHandle
     */
    public function handleTest(Result $result, Response|ResponseInterface $expected): void
    {
        $request = self::createStub(Request::class);
        $container = self::createMock(Container::class);
        $sut = new ResponseJsonFlat($container);

        $container->expects(self::once())
            ->method('get')
            ->with(Result::class)
            ->willReturn($result);

        $actual = $sut->handle($request, fn($var) => new Response());

        self::assertEquals($expected, $actual);
    }
}
