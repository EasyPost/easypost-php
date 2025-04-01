<?php

namespace EasyPost\Test\Mocking;

use EasyPost\Test\Mocking\MockRequest;

class MockingUtility
{
    /**
     * @var array<MockRequest>
     */
    public array $mockRequests;

    /**
     * Construct a new MockingUtility.
     *
     * @param array<MockRequest> $mockRequests
     */
    public function __construct(array $mockRequests)
    {
        $this->mockRequests = $mockRequests;
    }

    /**
     * Finds a matching mocked request.
     *
     * @param string $method
     * @param string $url
     * @return mixed
     */
    public function findMatchingMockRequest($method, $url): mixed
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
