<?php

namespace EasyPost\Test\mocking;

class MockRequestResponseInfo
{
    public int $statusCode;

    public string $body;

    public function __construct(int $statusCode, string $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }
}
