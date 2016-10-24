<?php
/**
 * Provides testing for the teststrapper which in the most part is the loading and enumeration of test files
 */
class TestStrapTest extends UnitTestCase
{
    /**
     * Tests that the testfixture loads without an error with a valid test folder
     *
     */
    function testStrapLoad()
    {
        $testStrap = new \Apprecie\Library\Testing\TestStrap(realpath(__DIR__));
        $this->assertTrue($testStrap->count() > 0, 'Failed to register this test in count.');
    }
}