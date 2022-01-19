<?php

namespace EasyPost\Test;

use VCR\VCR;
use \EasyPost\User;
use \EasyPost\EasyPost;

EasyPost::setApiKey(getenv('API_KEY'));

class UserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up VCR before running tests in this file
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        VCR::turnOn();
    }

    /**
     * Spin down VCR after running tests
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test the creation of a Tracker
     *
     * @return void
     */
    public function testUpdateBrand()
    {
        VCR::insertCassette('users/updateBrand.yml');

        $user = User::retrieve_me();

        $brand = $user->update_brand(array(
            'color' => '#fff'
        ));

        $this->assertEquals($brand->color, '#fff');
    }
}
