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

        $authenticatedUser = User::retrieve_me();

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

        $user = User::retrieve_me();

        $this->assertInstanceOf('\EasyPost\User', $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
    }

    /**
     * Test updating the authenticated user.
     */
    public function testUpdate()
    {
        TestUtil::setupCassette('users/update.yml');

        $user = User::retrieve_me();

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

        $response = $user->delete();

        $this->assertNotNull($response);
    }

    /**
     * Test retrieving all API keys.
     */
    public function testAllApiKeys()
    {
        TestUtil::setupCassette('users/all_api_keys.yml');

        $user = User::retrieve_me();

        $apiKeys = $user::all_api_keys();

        $this->assertNotNull($apiKeys['keys']);
        $this->assertNotNull($apiKeys['children']);

        // TODO: When the output of this function is fixed, swap the tests for the below
        // $this->assertContainsOnlyInstancesOf('\EasyPost\ApiKey', $apiKeys);
        // foreach ($apiKeys['children'] as $child) {
        //     $this->assertContainsOnlyInstancesOf('\EasyPost\ApiKey', $child['keys']);
        // }
    }

    /**
     * Test retrieving the authenticated user's API keys.
     */
    public function testAuthenticatedUserApiKeys()
    {
        TestUtil::setupCassette('users/authenticated_user_api_keys.yml');

        $user = User::retrieve_me();

        $apiKeys = $user->api_keys();

        $this->assertNotNull($apiKeys['production']);
        $this->assertNotNull($apiKeys['test']);

        // TODO: When the output of this function is fixed, swap the tests for the below
        // $this->assertContainsOnlyInstancesOf('\EasyPost\ApiKey', $apiKeys);
    }

    /**
     * Test retrieving the authenticated user's API keys.
     */
    public function testChildUserApiKeys()
    {
        TestUtil::setupCassette('users/child_user_api_keys.yml');

        $user = User::create([
            'name' => 'Test User',
        ]);
        $childUser = User::retrieve($user->id);

        $apiKeys = $childUser->api_keys();

        $this->assertNotNull($apiKeys['production']);
        $this->assertNotNull($apiKeys['test']);

        // TODO: When the output of this function is fixed, swap the tests for the below
        // $this->assertContainsOnlyInstancesOf('\EasyPost\ApiKey', $apiKeys);

        $user->delete(); // Delete the user once done so we don't pollute with hundreds of child users
    }

    /**
     * Test retrieving a user's paginated API keys.
     */
    public function testBetaUserPaginatedApiKeys()
    {
        TestUtil::setupCassette('users/beta_user_paginated_api_keys.yml');

        $pageSize = 1;

        // Have to test with "me" user due to server-side user restrictions
        $user = User::retrieve_me();

        $apiKeys = $user->paginated_api_keys([
            'page_size' => $pageSize,
        ]);

        $this->assertNotNull($apiKeys['api_keys']);
        $this->assertCount($pageSize, $apiKeys['api_keys']);
        $this->assertNotNull($apiKeys['has_more']);
        $this->assertArrayHasKey('active', $apiKeys['api_keys'][0]);
        $this->assertArrayHasKey('key', $apiKeys['api_keys'][0]);
        $this->assertArrayHasKey('id', $apiKeys['api_keys'][0]);

        // Retrieve the next page of API keys if there are more
        if ($apiKeys['has_more']) {
            $firstId = $apiKeys['api_keys'][0]['id'];
            $apiKeys = $user->paginated_api_keys([
                'page_size' => $pageSize,
                'after_id' => $firstId,
            ]);

            $this->assertNotNull($apiKeys['api_keys']);
            // Should have gotten a different page of API keys
            $this->assertNotEquals($firstId, $apiKeys['api_keys'][0]['id']);
        }
    }

    /**
     * Test updating the authenticated user's Brand.
     */
    public function testUpdateBrand()
    {
        TestUtil::setupCassette('users/brand.yml');

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
