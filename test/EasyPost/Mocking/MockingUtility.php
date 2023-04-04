<?php

namespace EasyPost\Test\Mocking;

class MockingUtility
{
    public array $mockRequests;

    /**
     * Construct a new MockingUtility.
     *
     * @param array $mockRequests
     */
    public function __construct(MockRequest ...$mockRequests)
    {
        $this->mockRequests = $mockRequests;
    }

    public function findMatchingMockRequest($method, $url)
    {
        foreach ($this->mockRequests as $mockRequest) {
            $methodMatches = $mockRequest->matchRule->method === ''
                || $mockRequest->matchRule->method === $method;
            $urlMatches = $mockRequest->matchRule->urlRegexPattern == ''
                || preg_match($mockRequest->matchRule->urlRegexPattern, $url) >= 1;  // limit to exactly one match?
            if ($methodMatches && $urlMatches) {
                return $mockRequest;
            }
        }

        return null;
    }
}
