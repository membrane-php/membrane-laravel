<?php

declare(strict_types=1);


use Membrane\Laravel\ToSymfony;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @covers \Membrane\Laravel\ToSymfony
 */
class ToSymfonyTest extends TestCase
{
    /** @test */
    public function invokeTest(): void
    {
        $sut = new ToSymfony();
        $request = new \Nyholm\Psr7\Response();

        $actual = $sut($request);

        self::assertInstanceOf(SymfonyResponse::class, $actual);
    }
}
