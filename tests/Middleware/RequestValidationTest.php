<?php

declare(strict_types=1);

namespace Membrane\Laravel\Tests\Middleware;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Membrane\Laravel\ApiProblemBuilder;
use Membrane\Laravel\Middleware\RequestValidation;
use Membrane\Laravel\ToPsr7;
use Membrane\Laravel\ToSymfony;
use Membrane\OpenAPI\Exception\CannotProcessSpecification;
use Membrane\OpenAPIReader\ValueObject\Valid\Enum\Method;
use Membrane\Result\Result;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequestValidation::class)]
#[UsesClass(ApiProblemBuilder::class)]
#[UsesClass(ToPsr7::class)]
#[UsesClass(ToSymfony::class)]
class RequestValidationTest extends TestCase
{
    #[Test]
    public function registersResultInstanceInContainer(): void
    {
        $url = '/pets?limit=5&tags=cat&tags=tabby';
        $expected = Result::valid([
            'path' => [],
            'query' => ['limit' => 5, 'tags' => ['cat', 'tabby']],
            'header' => [],
            'cookie' => [],
            'body' => '',
            'request' => ['method' => 'get', 'operationId' => 'findPets'],
        ]);

        $apiProblemBuilder = self::createStub(ApiProblemBuilder::class);
        $container = self::createMock(Container::class);
        $sut = new RequestValidation(
            __DIR__ . '/../fixtures/petstore-expanded.json',
            $apiProblemBuilder,
            $container
        );

        $container->expects(self::once())
            ->method('instance')
            ->with(Result::class, $expected);

        $sut->handle(Request::create($url), fn($var) => new Response());
    }

    public static function dataSetsThatThrowCannotProcessSpecification(): array
    {
        return [
            'path not found' => [
                '/hats',
                Method::GET,
                CannotProcessSpecification::pathNotFound(
                    'petstore-expanded.json',
                    '/hats'
                ),
            ],
            'method not found' => [
                '/pets',
                Method::DELETE,
                CannotProcessSpecification::methodNotFound(
                    Method::DELETE->value
                ),
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataSetsThatThrowCannotProcessSpecification')]
    public function catchesCannotProcessSpecification(
        string $path,
        Method $method,
        CannotProcessSpecification $expected
    ): void {
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

        $sut->handle(
            Request::create($path, $method->value),
            fn($p) => new Response()
        );
    }
}
