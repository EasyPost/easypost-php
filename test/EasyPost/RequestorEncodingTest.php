<?php

namespace EasyPost;

use EasyPost\Constant\Constants;
use EasyPost\Test\Mocking\MockingUtility;
use EasyPost\Test\Mocking\MockRequest;
use EasyPost\Test\Mocking\MockRequestMatchRule;
use EasyPost\Test\Mocking\MockRequestResponseInfo;
use PHPUnit\Framework\TestCase;

class RequestorEncodingTest extends TestCase
{
    private function getMockClient(): EasyPostClient
    {
        $mockingUtility = new MockingUtility([
            new MockRequest(
                new MockRequestMatchRule('post', '/v2\/shipments$/'),
                new MockRequestResponseInfo(200, '{}')
            ),
        ]);

        return new EasyPostClient(
            (string)getenv('EASYPOST_TEST_API_KEY'),
            Constants::TIMEOUT,
            Constants::API_BASE,
            $mockingUtility
        );
    }

    public function testEmptyArrayRemainsArrayInRequestBody(): void
    {
        $client = $this->getMockClient();
        $requestPayload = null;

        $client->subscribeToRequestHook(function (array $args) use (&$requestPayload): void {
            $requestPayload = $args['request_body'];
        });

        $client->makeApiCall('post', '/shipments', [
            'shipment' => [
                'options' => [],
            ],
        ]);

        $this->assertIsArray($requestPayload);
        $this->assertArrayHasKey('shipment', $requestPayload);
        $this->assertArrayHasKey('options', $requestPayload['shipment']);
        $this->assertSame([], $requestPayload['shipment']['options']);
    }

    public function testEmptyStdClassBecomesEmptyJsonObjectInRequestBody(): void
    {
        $client = $this->getMockClient();
        $requestPayload = null;

        $client->subscribeToRequestHook(function (array $args) use (&$requestPayload): void {
            $requestPayload = $args['request_body'];
        });

        $client->makeApiCall('post', '/shipments', [
            'shipment' => [
                'options' => (object)[],
            ],
        ]);

        $this->assertIsArray($requestPayload);
        $this->assertArrayHasKey('shipment', $requestPayload);
        $this->assertArrayHasKey('options', $requestPayload['shipment']);
        $this->assertIsObject($requestPayload['shipment']['options']);
        $this->assertSame('{}', json_encode($requestPayload['shipment']['options']));
    }

    public function testNonEmptyStdClassIsEncodedAsObject(): void
    {
        $client = $this->getMockClient();
        $requestPayload = null;

        $client->subscribeToRequestHook(function (array $args) use (&$requestPayload): void {
            $requestPayload = $args['request_body'];
        });

        $client->makeApiCall('post', '/shipments', [
            'shipment' => [
                'options' => (object)[
                    'incoterm' => 'DDP',
                    'currency' => '',
                ],
            ],
        ]);

        $this->assertIsArray($requestPayload);
        $this->assertArrayHasKey('shipment', $requestPayload);
        $this->assertArrayHasKey('options', $requestPayload['shipment']);
        $this->assertIsObject($requestPayload['shipment']['options']);
        $this->assertSame('DDP', $requestPayload['shipment']['options']->incoterm); // @phpstan-ignore-line
        $this->assertFalse(property_exists($requestPayload['shipment']['options'], 'currency'));
    }
}
