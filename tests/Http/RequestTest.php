<?php

declare(strict_types=1);

namespace Membrane\Laravel\Http;

use Membrane\Result\Result;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Membrane\Laravel\Http\Request
 */
class RequestTest extends TestCase
{

    /** @test */
    public function createFromResultTest(): void
    {
        $sut = Request::createFromResult(self::createStub(Result::class), new \Illuminate\Http\Request());

        self::assertInstanceOf(\Illuminate\Http\Request::class, $sut);
    }

    /** @test */
    public function getResultTest(): void
    {
        $result = Result::valid(1);

        $sut = Request::createFromResult($result, new \Illuminate\Http\Request());

        self::assertEquals($result, $sut->getResult());
    }
}
