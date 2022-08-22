<?php

namespace EasyPost\Test;

// Manually require the library to ensure there are no import errors (eg: when the autoloader is not used)
require 'lib/easypost.php';

class RequireTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that no errors are thrown when we import the library without using the autoloader.
     * Things like missing or extra imports should be caught by this. The actual assertion here
     * doesn't matter, only that an import/require error isn't thrown when the test suite runs.
     */
    public function testRequireLibrary()
    {
        $this->expectNotToPerformAssertions();
    }
}
