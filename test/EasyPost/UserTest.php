<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\User;
use Exception;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private static EasyPostClient $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));
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
    public function testCreate(): void
    {
        TestUtil::setupCassette('users/create.yml');

        $user = self::$client->user->create([
            'name' => 'Test User',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
        $this->assertEquals('Test User', $user->name);

        // Delete the user once done so we don't pollute with hundreds of child users
        self::$client->user->delete($user->id);
    }

    /**
     * Test retrieving a user.
     */
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('users/retrieve.yml');

        $authenticatedUser = self::$client->user->retrieveMe();

        $user = self::$client->user->retrieve($authenticatedUser->id);

        $this->assertInstanceOf(User::class, $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
    }

    /**
     * Test retrieving the authenticated user.
     */
    public function testRetrieveMe(): void
    {
        TestUtil::setupCassette('users/retrieveMe.yml');

        $user = self::$client->user->retrieveMe();

        $this->assertInstanceOf(User::class, $user);
        $this->assertStringMatchesFormat('user_%s', $user->id);
    }

    /**
     * Test retrieving all child users.
     */
    public function testAllChildren(): void
    {
        TestUtil::setupCassette('users/allChildren.yml');

        $children = self::$client->user->allChildren([
            'page_size' => Fixture::pageSize(),
        ]);

        $userArray = $children['children'];

        $this->assertLessThanOrEqual($userArray, Fixture::pageSize());
        $this->assertNotNull($children['has_more']);
        $this->assertContainsOnlyInstancesOf(User::class, $userArray);
    }

    /**
     * Test retrieving next page.
     * @noinspection RedundantSuppression
     * @throws Exception
     */
    public function testGetNextPageOfChildren(): void
    {
        TestUtil::setupCassette('users/getNextPageOfChildren.yml');

        try {
            $children = self::$client->user->allChildren([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->user->getNextPageOfChildren($children, Fixture::pageSize());

            $firstIdOfFirstPage = $children['children'][0]->id;
            $secondIdOfSecondPage = $nextPage['children'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->expectNotToPerformAssertions();
        } catch (Exception $error) {
            /** @noinspection PhpExceptionImmediatelyRethrownInspection */
            throw $error;
        }
    }

    /**
     * Test updating the authenticated user.
     */
    public function testUpdate(): void
    {
        TestUtil::setupCassette('users/update.yml');

        $user = self::$client->user->retrieveMe();

        $testName = 'New Name';
        $params = [];
        $params['name'] = $testName;

        $updatedUser = self::$client->user->update($user->id, $params);

        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertStringMatchesFormat('user_%s', $updatedUser->id);
        $this->assertEquals($testName, $updatedUser->name);
    }

    /**
     * Test deleting a child user.
     */
    public function testDelete(): void
    {
        TestUtil::setupCassette('users/delete.yml');

        $user = self::$client->user->create([
            'name' => 'Test User',
        ]);

        try {
            self::$client->user->delete($user->id);
            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * Test updating the authenticated user's Brand.
     */
    public function testUpdateBrand(): void
    {
        TestUtil::setupCassette('users/brand.yml');

        $user = self::$client->user->retrieveMe();

        $color = '#123456';

        $brand = self::$client->user->updateBrand(
            $user->id,
            ['color' => $color]
        );

        $this->assertInstanceOf('\EasyPost\Brand', $brand);
        $this->assertStringMatchesFormat('brd_%s', $brand->id);
        $this->assertEquals($color, $brand->color);
    }
}
