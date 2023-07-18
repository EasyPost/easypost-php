<?php

namespace EasyPost\Test;

use DateTime;
use EasyPost\EasyPostClient;

class HookTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Make assertions about a request once the RequestHook fires.
     */
    public function requestTest($args)
    {
        $this->assertEquals('post', $args['method']);
        $this->assertEquals('https://api.easypost.com/v2/parcels', $args['path']);
        $this->assertArrayHasKey('parcel', $args['request_body']);
        $this->assertArrayHasKey('Authorization', $args['headers']);
        $this->assertIsFloat($args['request_timestamp']);
        $this->assertEquals(13, strlen($args['request_uuid']));
    }

    /**
     * Test that we fire a RequestHook prior to making an HTTP request.
     */
    public function testRequestHooks()
    {
        TestUtil::setupCassette('hooks/request.yml');

        self::$client->subscribeToRequestHook([$this, 'requestTest']);
        self::$client->parcel->create(Fixture::basicParcel());
    }

    /**
     * Make assertions about a response once the ResponseHook fires.
     */
    public function responseTest($args)
    {
        $this->assertEquals(201, $args['http_status']);
        $this->assertEquals('post', $args['method']);
        $this->assertEquals('https://api.easypost.com/v2/parcels', $args['path']);
        $this->assertNotNull(json_decode($args['response_body'], true)['object']);
        $this->assertArrayHasKey('location', $args['headers']);
        $this->assertTrue($args['response_timestamp'] > $args['request_timestamp']);
        $this->assertEquals(13, strlen($args['request_uuid']));
    }

    /**
     * Test that we fire a ResponseHook after receiving an HTTP response.
     */
    public function testResponseHooks()
    {
        TestUtil::setupCassette('hooks/response.yml');

        self::$client->subscribeToResponseHook([$this, 'responseTest']);
        self::$client->parcel->create(Fixture::basicParcel());
    }

    /**
     * This function should never run since we unsubscribe from HTTP hooks.
     */
    public function failIfSubscribed()
    {
        throw new \Exception('Unsubscribing from HTTP hooks did not work as intended');
    }

    /**
     * Test that we do not fire a hook once unsubscribed.
     */
    public function testUnsubscribeHooks()
    {
        TestUtil::setupCassette('hooks/unsubscribe.yml');

        self::$client->subscribeToRequestHook([$this, 'failIfSubscribed']);
        self::$client->unsubscribeFromRequestHook([$this, 'failIfSubscribed']);

        self::$client->subscribeToResponseHook([$this, 'failIfSubscribed']);
        self::$client->unsubscribeFromResponseHook([$this, 'failIfSubscribed']);

        self::$client->parcel->create(Fixture::basicParcel());
    }
}
