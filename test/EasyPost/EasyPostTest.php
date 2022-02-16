<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;

class EasyPostTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        // Set all the defaults again so other tests don't fail
        EasyPost::setApiBase('https://api.easypost.com/v2');
        EasyPost::setApiVersion('2');
        EasyPost::setConnectTimeout(30000);
        EasyPost::setResponseTimeout(60000);
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

        $this->assertEquals($test_base, $api_base);
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

        $this->assertEquals($test_version, $api_version);
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

        $this->assertEquals($test_timeout, $connection_timeout);
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

        $this->assertEquals($test_timeout, $connection_timeout);
    }
}
