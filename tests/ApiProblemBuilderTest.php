<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Membrane\Renderer\Renderer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Laravel\ApiProblemBuilder
 * @uses   \Membrane\Laravel\ToSymfony
 */
class ApiProblemBuilderTest extends TestCase
{

    /** @test */
    public function buildsApiProblemImplementingServerResponseInterface(): void
    {
        $expected = [
            'errors' => [
                'id' => ['must be an integer'],
            ],
            'title' => 'Request payload failed validation',
            'status' => 400,
            'type' => 'about:blank',

        ];

        $sut = new ApiProblemBuilder(400, 'about:blank');
        $renderer = self::createMock(Renderer::class);
        $renderer->expects(self::once())
            ->method('jsonSerialize')
            ->willReturn(['id' => ['must be an integer']]);

        $actual = $sut->build($renderer);

        self::assertEquals($expected, json_decode($actual->getContent(), true));
    }

}
