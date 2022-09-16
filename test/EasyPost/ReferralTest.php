<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Referral;
use VCR\VCR;

class ReferralTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('PARTNER_USER_PROD_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a child user.
     */
    public function testCreate()
    {
        VCR::insertCassette('referrals/create.yml');

        $referral = Referral::create([
            'name' => 'Test Referral',
            'email' => 'test@test.com',
            'phone' => '8888888888'
        ]);

        $this->assertInstanceOf('\EasyPost\User', $referral);
        $this->assertStringMatchesFormat('user_%s', $referral->id);
        $this->assertEquals('Test Referral', $referral->name);
    }

    /**
     * Test retrieving a child user.
     */
    public function testAll()
    {
        VCR::insertCassette('referrals/all.yml');

        $referralUsers = Referral::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $referralUsersArray = $referralUsers['referral_customers'];

        $this->assertLessThanOrEqual($referralUsersArray, Fixture::pageSize());
        $this->assertNotNull($referralUsers['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\User', $referralUsersArray);
    }

    /**
     * Test retrieving the authenticated user.
     */
    public function testUpdateEmail()
    {
        VCR::insertCassette('referrals/updateEmail.yml');

        $referralUsers = Referral::all();

        $referralUser = Referral::update_email("email@example.com", $referralUsers['referral_customers'][0]['id']);

        $this->assertEquals($referralUser, true);
    }
}
