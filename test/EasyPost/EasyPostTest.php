<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;

class EasyPostTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Reset API instance for future tests
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        EasyPost::setApiBase(EasyPost::$apiBase);
        EasyPost::setApiVersion(EasyPost::$apiVersion);
        EasyPost::setConnectTimeout(EasyPost::$connectTimeout);
        EasyPost::setResponseTimeout(EasyPost::$responseTimeout);

        // Sleep to avoid PHPUnit running off to the next test before we are finished (known race condition).
        usleep(500);
    }

    /**
     * Test setting and getting the API key.
     *
     * @return void
     */
    public function testApiKey()
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));
        $api_key = EasyPost::getApiKey();

        $this->assertNotNull($api_key);
    }

    /**
     * Test setting and getting the API base.
     *
     * @return void
     */
    public function testApiBase()
    {
        $test_base = 'http://example.com';

        EasyPost::setApiBase($test_base);
        $api_base = EasyPost::getApiBase();

        $this->assertEquals($api_base, $test_base);
    }

    /**
     * Test setting and getting the API version.
     *
     * @return void
     */
    public function testApiVersion()
    {
        $test_version = '100';

        EasyPost::setApiVersion($test_version);
        $api_version = EasyPost::getApiVersion();

        $this->assertEquals($api_version, $test_version);
    }

    /**
     * Test setting and getting the connection timeout.
     *
     * @return void
     */
    public function testConnectionTimeout()
    {
        $test_timeout = '1';

        EasyPost::setConnectTimeout($test_timeout);
        $connection_timeout = EasyPost::getConnectTimeout();

        $this->assertEquals($connection_timeout, $test_timeout);
    }

    /**
     * Test setting and getting the request timeout.
     *
     * @return void
     */
    public function testRequestTimeout()
    {
        $test_timeout = '1';

        EasyPost::setResponseTimeout($test_timeout);
        $connection_timeout = EasyPost::getResponseTimeout();

        $this->assertEquals($connection_timeout, $test_timeout);
    }
}
