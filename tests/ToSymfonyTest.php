<?php

declare(strict_types=1);

namespace Membrane\Laravel\Tests;

use Membrane\Laravel\ToSymfony;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

#[CoversClass(ToSymfony::class)]
class ToSymfonyTest extends TestCase
{
    #[Test]
    public function itConvertsPsrResponseToSymfonyResponse(): void
    {
        $sut = new ToSymfony();
        $request = new \Nyholm\Psr7\Response();

        $actual = $sut($request);

        self::assertInstanceOf(SymfonyResponse::class, $actual);
    }
}
