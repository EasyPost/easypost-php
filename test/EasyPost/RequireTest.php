<?php

namespace EasyPost\Test;

use PHPUnit\Framework\TestCase;

class RequireTest extends TestCase
{
    /**
     * Tests that no errors are thrown when we import the library without using the autoloader.
     * Things like missing or extra imports should be caught by this. The actual assertion here
     * doesn't matter, only that an import/require error isn't thrown when the test suite runs.
     */
    public function testRequireLibrary(): void
    {
        // Manually require the library to ensure there are no import errors (eg: when the autoloader is not used)
        require_once('lib/easypost.php');
        $this->expectNotToPerformAssertions();
    }
}
