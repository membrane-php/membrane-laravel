<?php

declare(strict_types=1);

namespace Membrane\Laravel;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Membrane\Laravel\ToPsr7
 */
class ToPsr7Test extends TestCase
{

    /** @test */
    public function invokeTest(): void
    {
        $sut = new ToPsr7();
        $request = Request::create('/pets/1');

        $actual = $sut($request);

        self::assertInstanceOf(ServerRequestInterface::class, $actual);
        self::assertSame('/pets/1', $actual->getUri()->getPath());
        self::assertSame('GET', $actual->getMethod());
    }

}
