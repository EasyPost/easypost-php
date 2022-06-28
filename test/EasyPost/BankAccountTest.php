<?php

namespace EasyPost\Test;

use EasyPost\BankAccount;
use EasyPost\EasyPost;
use VCR\VCR;

class BankAccountTest extends \PHPUnit\Framework\TestCase
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
     * Test funding wallet by using bank account.
     *
     * @return void
     */
    public function testFund()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real bank account in tests.');

        VCR::insertCassette('bank_account/fund.yml');

        $bank_account = BankAccount::fund(2000, 'primary');

        $this->assertTrue($bank_account != null);
    }

    /**
     * Test deleting a bank account.
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real bank account in tests.');

        VCR::insertCassette('bank_account/delete.yml');

        $delete_bank_account = BankAccount::delete('bank_1234');

        $this->assertTrue($delete_bank_account != null);
    }
}
