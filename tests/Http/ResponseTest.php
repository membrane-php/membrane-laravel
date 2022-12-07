<?php

declare(strict_types=1);

namespace Membrane\Laravel\Http;

use Membrane\Result\Result;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

/**
 * @covers \Membrane\Laravel\Http\Response
 */
class ResponseTest extends TestCase
{

    /** @test */
    public function constructTest(): void
    {
        $result = Result::valid(1);

        $sut = new Response(result: $result);

        assertEquals($result, $sut->result);
    }
}
