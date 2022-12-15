<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Membrane\Renderer\Renderer;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Laravel\ApiProblemBuilder
 */
class ApiProblemBuilderTest extends TestCase
{

    /** @test */
    public function buildsApiProblemImplementingServerResponseInterface(): void
    {
        $expected = new Response(
            status: 400,
            headers: ['Content-Type' => ['application/problem+json']],
            body: '{"errors":{"id":["error message"]}',
        );

        $sut = new ApiProblemBuilder();
        $renderer = self::createMock(Renderer::class);
        $renderer->expects(self::once())
            ->method('jsonSerialize')
            ->willReturn('{"errors":{"id":["error message"]},"title":"Request payload fail');

        $actual = $sut->build($renderer);

        self::assertEquals($expected, $actual);
    }

}
