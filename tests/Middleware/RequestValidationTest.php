<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Membrane\Laravel\ApiProblemBuilder;
use Membrane\OpenAPI\Exception\CannotProcessRequest;
use Membrane\OpenAPI\Method;
use Membrane\Result\Result;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Laravel\Middleware\RequestValidation
 * @uses   \Membrane\Laravel\ApiProblemBuilder
 * @uses   \Membrane\Laravel\ToPsr7
 * @uses   \Membrane\Laravel\ToSymfony
 */
class RequestValidationTest extends TestCase
{
    /** @test */
    public function registersResultInstanceInContainer(): void
    {
        $url = '/pets?limit=5&tags[]=cat&tags[]=tabby';
        $expected = Result::valid([
                'path' => [],
                'query' => ['limit' => 5, 'tags' => ['cat', 'tabby']],
                'header' => [],
                'cookie' => [],
                'body' => '',
            ]
        );
        $apiProblemBuilder = self::createStub(ApiProblemBuilder::class);
        $container = self::createMock(Container::class);
        $sut = new RequestValidation(__DIR__ . '/../fixtures/petstore-expanded.json', $apiProblemBuilder, $container);

        $container->expects(self::once())
            ->method('instance')
            ->with(Result::class, $expected);

        $sut->handle(Request::create($url), fn($var) => new Response());
    }

    public static function dataSetsThatThrowCannotProcessRequest(): array
    {
        return [
            'path not found' => [
                '/hats',
                Method::GET,
                CannotProcessRequest::pathNotFound('petstore-expanded.json', '/hats'),
            ],
            'method not found' => [
                '/pets',
                Method::DELETE,
                CannotProcessRequest::methodNotFound(Method::DELETE->value),
            ],
            // TODO test 406 from unsupported content-types once Membrane is reading content-types from requests
        ];
    }


    /**
     * @test
     * @dataProvider dataSetsThatThrowCannotProcessRequest
     */
    public function catchesCannotProcessRequest(string $path, Method $method, CannotProcessRequest $expected): void
    {
        $apiProblemBuilder = self::createMock(ApiProblemBuilder::class);
        $sut = new RequestValidation(
            __DIR__ . '/../fixtures/petstore-expanded.json',
            $apiProblemBuilder,
            self::createStub(Container::class)
        );

        $apiProblemBuilder
            ->expects($this->once())
            ->method('buildFromException')
            ->with($expected);

        $sut->handle(Request::create($path, $method->value), fn($p) => new Response());
    }

}
