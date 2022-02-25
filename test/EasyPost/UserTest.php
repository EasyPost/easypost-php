<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\User;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

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
        $this->markTestSkipped('This endpoint returns the child user keys in plain text, do not run this test.');

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

        $user = User::retrieve(Fixture::child_user_id());

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
    }

    /**
     * Test retrieving the authenticated user.
     *
     * @return User
     */
    public function testRetrieveMe()
    {
        VCR::insertCassette('users/retrieveMe.yml');

        $user = User::retrieve_me();

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);

        // Return so other tests can reuse this object
        return $user;
    }

    /**
     * Test updating the authenticated user.
     *
     * @param User $user
     * @return void
     * @depends testRetrieveMe
     */
    public function testUpdate(User $user)
    {
        VCR::insertCassette('users/update.yml');

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
     * @param User $user
     * @return void
     * @depends testCreate
     */
    public function testDelete(User $user)
    {
        $this->markTestSkipped('Due to our inability to create child users securely, we must also skip deleting them as we cannot replace the deleted ones easily.');

        VCR::insertCassette('users/delete.yml');

        $user->delete();
    }

    /**
     * Test retrieving all API keys.
     *
     * @param User $user
     * @return void
     * @depends testRetrieveMe
     */
    public function testAllApiKeys(User $user)
    {
        $this->markTestSkipped('API keys are returned as plaintext, do not run this test.');

        VCR::insertCassette('users/all_api_keys.yml');

        $user->all_api_keys();
    }

    /**
     * Test retrieving the authenticated user's API keys.
     *
     * @param User $user
     * @return void
     * @depends testRetrieveMe
     */
    public function testApiKeys(User $user)
    {
        $this->markTestSkipped('API keys are returned as plaintext, do not run this test.');

        VCR::insertCassette('users/api_keys.yml');

        $user->api_keys();
    }

    /**
     * Test updating the authenticated user's Brand.
     *
     * @param User $user
     * @return void
     * @depends testRetrieveMe
     */
    public function testUpdateBrand(User $user)
    {
        VCR::insertCassette('users/brand.yml');

        $color = '#123456';

        $brand = $user->update_brand([
            'color' => $color,
        ]);

        $this->assertInstanceOf('\EasyPost\Brand', $brand);
        $this->assertStringMatchesFormat('brd_%s', $brand->id);
        $this->assertEquals($color, $brand->color);
    }
}
