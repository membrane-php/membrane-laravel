<?php

declare(strict_types=1);

namespace Membrane\Laravel\Http;

use Membrane\Result\Result;

class Request extends \Illuminate\Http\Request
{
    private Result $result;

    public static function createFromResult(Result $result, \Illuminate\Http\Request $request): self
    {
        $instance = new self();
        self::createFrom($request, $instance);
        $instance->result = $result;

        return $instance;
    }

    public function getResult(): Result
    {
        return $this->result;
    }
}
