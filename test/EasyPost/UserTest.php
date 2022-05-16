<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\User;
use VCR\VCR;

class UserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_PROD_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a child user.
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('users/create.yml');

        $user = User::create([
            'name' => 'Test User',
        ]);

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
        $this->assertEquals('Test User', $user->name);
    }

    /**
     * Test retrieving a child user.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('users/retrieve.yml');

        $authenticated_user = User::retrieve_me();

        $user = User::retrieve($authenticated_user['id']);

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
    }

    /**
     * Test retrieving the authenticated user.
     *
     * @return void
     */
    public function testRetrieveMe()
    {
        VCR::insertCassette('users/retrieveMe.yml');

        $user = User::retrieve_me();

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
    }

    /**
     * Test updating the authenticated user.
     *
     * @return void
     */
    public function testUpdate()
    {
        VCR::insertCassette('users/update.yml');

        $user = User::retrieve_me();

        $test_phone = '5555555555';

        $user->phone = $test_phone;
        $user->save();

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
        $this->assertEquals($test_phone, $user->phone);
    }

    /**
     * Test deleting a child user.
     *
     * @return void
     */
    public function testDelete()
    {
        VCR::insertCassette('users/delete.yml');

        $user = User::create([
            'name' => 'Test User',
        ]);

        $response = $user->delete();

        $this->assertNotNull($response);
    }

    /**
     * Test retrieving all API keys.
     *
     * @return void
     */
    public function testAllApiKeys()
    {
        VCR::insertCassette('users/all_api_keys.yml');

        $user = User::retrieve_me();

        $api_keys = $user->all_api_keys();

        $this->assertNotNull($api_keys->keys);
    }

    /**
     * Test retrieving the authenticated user's API keys.
     *
     * @return void
     */
    public function testApiKeys()
    {
        $this->markTestSkipped("Due to redacting the `children` key in the cassettes, we can't run this test as the redacted key is then a string instead of an array and breaks.");

        VCR::insertCassette('users/api_keys.yml');

        $user = User::retrieve_me();

        $api_keys = $user->api_keys();

        $this->assertNotNull($api_keys->keys);
    }

    /**
     * Test updating the authenticated user's Brand.
     *
     * @return void
     */
    public function testUpdateBrand()
    {
        VCR::insertCassette('users/brand.yml');

        $user = User::retrieve_me();

        $color = '#123456';

        $brand = $user->update_brand([
            'color' => $color,
        ]);

        $this->assertInstanceOf('\EasyPost\Brand', $brand);
        $this->assertStringMatchesFormat('brd_%s', $brand->id);
        $this->assertEquals($color, $brand->color);
    }
}
