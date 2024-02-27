<?php

declare(strict_types=1);

namespace Membrane\Laravel\Tests;

use Illuminate\Http\Request;
use Membrane\Laravel\ToPsr7;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(ToPsr7::class)]
class ToPsr7Test extends TestCase
{
    #[Test]
    public function itConvertsLaravelRequestIntoPsrRequest(): void
    {
        $sut = new ToPsr7();
        $request = Request::create('/pets/1');

        $actual = $sut($request);

        self::assertInstanceOf(ServerRequestInterface::class, $actual);
        self::assertSame('/pets/1', $actual->getUri()->getPath());
        self::assertSame('GET', $actual->getMethod());
    }
}
