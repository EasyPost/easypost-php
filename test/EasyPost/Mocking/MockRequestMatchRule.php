<?php

namespace EasyPost\Test\Mocking;

class MockRequestMatchRule
{
    public string $method;
    public string $urlRegexPattern;

    public function __construct(string $method, string $urlRegexPattern)
    {
        $this->method = $method;
        $this->urlRegexPattern = $urlRegexPattern;
    }
}
