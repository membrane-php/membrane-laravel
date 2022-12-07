<?php

declare(strict_types=1);

namespace Membrane\Laravel\Http;

use Membrane\Result\Result;

class Response extends \Illuminate\Http\Response
{
    public function __construct(
        mixed $content = '',
        int $status = 200,
        array $headers = [],
        public readonly ?Result $result = null
    ) {
        parent::__construct($content, $status, $headers);
    }
}
