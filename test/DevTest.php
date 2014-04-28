<?php
#
# DevTest.php
#
# A simple test to verify PhpUnit is configured properly and demonstrate basic
# test practices for Neechy project. PhpUnit must be installed.
#
# Usage (run from Neechy root dir):
# > phpunit test/DevTest
#

class SampleNeechyTests extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public static function setUpBeforeClass() {}

    public static function tearDownAfterClass() {}

    public function setUp() {}

    public function tearDown() {}


    /**
     * Tests
     */
    public function testNotWrittenYet() {
        # see http://phpunit.de/manual/current/en/incomplete-and-skipped-tests.html
        #$this->markTestIncomplete('For future tests. Will report as incomplete.');
    }

    public function testTruth() {
        $this->assertTrue(true);
    }
}
