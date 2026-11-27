<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Test\Mocking\MockingUtility;
use EasyPost\Test\Mocking\MockRequest;
use EasyPost\Test\Mocking\MockRequestMatchRule;
use EasyPost\Test\Mocking\MockRequestResponseInfo;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    /** @var array<int, mixed> */
    private array $sentParams = [];

    /**
     * Build a mocked client that records the query params of every request.
     */
    private function client(string $urlPattern, string $responseBody): EasyPostClient
    {
        $client = new EasyPostClient('test_key', 60.0, 'https://api.easypost.com', new MockingUtility([
            new MockRequest(
                new MockRequestMatchRule('get', $urlPattern),
                new MockRequestResponseInfo(200, $responseBody)
            ),
        ]));
        $client->subscribeToRequestHook(function (array $args): void {
            $this->sentParams[] = $args['request_body'];
        });

        return $client;
    }

    /**
     * Test that the new cursor and page size win over the params of the previous request.
     */
    public function testGetNextPageCursorOverridesPreviousParams(): void
    {
        $client = $this->client('/v2\\/shipments$/', '{"shipments": [{"id": "shp_last"}], "has_more": true}');

        $shipments = $client->shipment->all(['before_id' => 'shp_old', 'page_size' => 5, 'purchased' => false]);
        $nextPage = $client->shipment->getNextPage($shipments, 2);

        $this->assertEquals('shp_last', $this->sentParams[1]['before_id']);
        $this->assertEquals(2, $this->sentParams[1]['page_size']);
        $this->assertFalse($this->sentParams[1]['purchased']);

        $client->shipment->getNextPage($nextPage);

        $this->assertEquals('shp_last', $this->sentParams[2]['before_id']);
        $this->assertEquals(5, $this->sentParams[2]['page_size']);
    }

    /**
     * Test that the new cursor wins over the params of the previous request for child users.
     */
    public function testGetNextPageOfChildrenCursorOverridesPreviousParams(): void
    {
        $client = $this->client('/v2\\/users\\/children$/', '{"children": [{"id": "user_last"}], "has_more": true}');

        $children = $client->user->allChildren(['after_id' => 'user_old', 'page_size' => 5]);
        $client->user->getNextPageOfChildren($children, 2);

        $this->assertEquals('user_last', $this->sentParams[1]['after_id']);
        $this->assertEquals(2, $this->sentParams[1]['page_size']);
    }

    /**
     * Test that the cursor keeps advancing when the first request had no params.
     */
    public function testGetNextPageAdvancesWithoutInitialParams(): void
    {
        $client = $this->client('/v2\\/shipments$/', '{"shipments": [{"id": "shp_last"}], "has_more": true}');

        $firstPage = $client->shipment->all();
        $secondPage = $client->shipment->getNextPage($firstPage);
        $lastShipment = $secondPage['shipments'][0];
        $lastShipment->id = 'shp_second';
        $client->shipment->getNextPage($secondPage);

        $this->assertEquals('shp_second', $this->sentParams[2]['before_id']);
    }
}
