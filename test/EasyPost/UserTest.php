<?php

namespace EasyPost\Test;

use EasyPost\User;

class UserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests('EASYPOST_PROD_API_KEY');
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating a child user.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('users/create.yml');

        $user = User::create([
            'name' => 'Test User',
        ]);

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
        $this->assertEquals('Test User', $user->name);

        $user->delete(); // Delete the user once done so we don't pollute with hundreds of child users
    }

    /**
     * Test retrieving a user.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('users/retrieve.yml');

        $authenticatedUser = User::retrieveMe();

        $user = User::retrieve($authenticatedUser['id']);

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
    }

    /**
     * Test retrieving the authenticated user.
     */
    public function testRetrieveMe()
    {
        TestUtil::setupCassette('users/retrieveMe.yml');

        $user = User::retrieveMe();

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
    }

    /**
     * Test updating the authenticated user.
     */
    public function testUpdate()
    {
        TestUtil::setupCassette('users/update.yml');

        $user = User::retrieveMe();

        $testPhone = '5555555555';

        $user->phone = $testPhone;
        $user->save();

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
        $this->assertEquals($testPhone, $user->phone);
    }

    /**
     * Test deleting a child user.
     */
    public function testDelete()
    {
        TestUtil::setupCassette('users/delete.yml');

        $user = User::create([
            'name' => 'Test User',
        ]);

        $user->delete();

        // This endpoint returns nothing so we only assert a failure doesn't happen
        $this->expectNotToPerformAssertions();
    }

    /**
     * Test retrieving all API keys.
     */
    public function testAllApiKeys()
    {
        TestUtil::setupCassette('users/allApiKeys.yml');

        $user = User::retrieveMe();

        $apiKeys = $user::allApiKeys();

        $this->assertContainsOnlyInstancesOf('\EasyPost\ApiKey', $apiKeys['keys']);
        foreach ($apiKeys['children'] as $child) {
            $this->assertContainsOnlyInstancesOf('\EasyPost\ApiKey', $child['keys']);
        }
    }

    /**
     * Test retrieving the authenticated user's API keys.
     */
    public function testAuthenticatedUserApiKeys()
    {
        TestUtil::setupCassette('users/authenticatedUserApiKeys.yml');

        $user = User::retrieveMe();

        $apiKeys = $user->apiKeys();

        $this->assertContainsOnlyInstancesOf('\EasyPost\ApiKey', $apiKeys);
    }

    /**
     * Test retrieving the authenticated user's API keys.
     */
    public function testChildUserApiKeys()
    {
        TestUtil::setupCassette('users/childUserApiKeys.yml');

        $user = User::create([
            'name' => 'Test User',
        ]);
        $childUser = User::retrieve($user->id);

        $apiKeys = $childUser->apiKeys();

        $this->assertContainsOnlyInstancesOf('\EasyPost\ApiKey', $apiKeys);

        $user->delete(); // Delete the user once done so we don't pollute with hundreds of child users
    }

    /**
     * Test updating the authenticated user's Brand.
     */
    public function testUpdateBrand()
    {
        TestUtil::setupCassette('users/brand.yml');

        $user = User::retrieveMe();

        $color = '#123456';

        $brand = $user->updateBrand([
            'color' => $color,
        ]);

        $this->assertInstanceOf('\EasyPost\Brand', $brand);
        $this->assertStringMatchesFormat('brd_%s', $brand->id);
        $this->assertEquals($color, $brand->color);
    }
}
