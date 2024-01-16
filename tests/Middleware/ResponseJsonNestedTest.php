<?php

declare(strict_types=1);

namespace Membrane\Laravel\Middleware;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Membrane\Laravel\ApiProblemBuilder;
use Membrane\Laravel\ToSymfony;
use Membrane\Result\FieldName;
use Membrane\Result\Message;
use Membrane\Result\MessageSet;
use Membrane\Result\Result;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


#[CoversClass(ResponseJsonNested::class)]
#[UsesClass(ApiProblemBuilder::class)]
#[UsesClass(ToSymfony::class)]
class ResponseJsonNestedTest extends TestCase
{
    public static function dataSetsToHandle(): array
    {
        return [
            'valid results return valid responses' => [
                Result::valid(1),
                new SymfonyResponse(),
            ],
            'invalid result returns response with ApiProblem' => [
                Result::invalid(
                    1,
                    new MessageSet(new FieldName('id', 'pet'), new Message('must be an integer', [])),
                    new MessageSet(new FieldName('pet'), new Message('%s is a required field', ['name']))
                ),
                (new SymfonyResponse(
                    content: '{"errors":{"errors":[],"fields":{"pet":{"errors":["name is a required field"],"fields":{"id":{"errors":["must be an integer"],"fields":[]}}}}},"title":"Request payload failed validation","type":"about:blank","status":400}',
                    status: 400,
                    headers: ['Content-Type' => ['application/problem+json']],
                ))->setProtocolVersion('1.1'),
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataSetsToHandle')]
    public function handleTest(Result $result, SymfonyResponse $expected): void
    {
        $request = self::createStub(Request::class);
        $container = self::createMock(Container::class);
        $apiProblemBuilder = new ApiProblemBuilder(400, 'about:blank', []);
        $sut = new ResponseJsonNested($container, $apiProblemBuilder);

        $container->expects(self::once())
            ->method('get')
            ->with(Result::class)
            ->willReturn($result);

        $actual = $sut->handle($request, fn($var) => new SymfonyResponse());

        self::assertSame($expected->getStatusCode(), $actual->getStatusCode());
        self::assertSame($expected->getContent(), $actual->getContent());
    }
}
