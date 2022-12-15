<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Membrane\Result\Result;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Laravel\Middleware\RequestValidation
 * @uses   \Membrane\Laravel\ToPsr7
 */
class RequestValidationTest extends TestCase
{
    /** @test */
    public function handleRegistersResultInstanceInContainer(): void
    {
        $expected = Result::valid([
                'path' => [],
                'query' => ['limit' => 5, 'tags' => ['cat', 'tabby']],
                'header' => [],
                'cookie' => [],
                'body' => '',
            ]
        );
        $container = self::createMock(Container::class);
        $sut = new RequestValidation(__DIR__ . '/../fixtures/petstore-expanded.json', $container);

        $container->expects(self::once())
            ->method('instance')
            ->with(Result::class, $expected);

        $sut->handle(Request::create('/pets?limit=5&tags[]=cat&tags[]=tabby'), fn($var) => new Response());
    }

}
