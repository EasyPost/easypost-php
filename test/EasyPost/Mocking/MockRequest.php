<?php

namespace EasyPost\Test\Mocking;

class MockRequest
{
    public MockRequestMatchRule $matchRule;
    public MockRequestResponseInfo $responseInfo;

    public function __construct(MockRequestMatchRule $matchRule, MockRequestResponseInfo $responseInfo)
    {
        $this->matchRule = $matchRule;
        $this->responseInfo = $responseInfo;
    }
}
