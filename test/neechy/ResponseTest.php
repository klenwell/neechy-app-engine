<?php
/**
 * test/neechy/ResponseTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/ResponseTest
 *
 */
require_once('../core/neechy/request.php');


class NeechyResponseTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
    }

    public function tearDown() {
    }

    /**
     * Tests
     */
    public function testInstantiates() {
        $response = new NeechyResponse();
        $this->assertInstanceOf('NeechyResponse', $response);
    }
}
