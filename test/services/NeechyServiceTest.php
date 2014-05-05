<?php
/**
 * test/services/NeechyServiceTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php services/NeechyServiceTest
 *
 */
require_once('../core/services/base.php');


class NeechyServiceTest extends PHPUnit_Framework_TestCase {

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
        $service = new NeechyService();
        $this->assertInstanceOf('NeechyService', $service);
    }
}
