<?php

namespace EasyPost\Test;

require 'lib/easypost.php';

class RequireTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that no errors are thrown when we import the library without using the autoloader.
     * Things like missing or extra imports should be caught by this. The actual assertion here
     * doesn't matter, only that an import/require error isn't thrown.
     */
    public function testRequireLibrary()
    {
        $apiBase = \EasyPost\EasyPost::getApiBase();
        $this->assertEquals('https://api.easypost.com/v2', $apiBase);
    }
}
