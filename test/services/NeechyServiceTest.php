<?php
/**
 * test/services/NeechyServiceTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php services/NeechyServiceTest
 *
 */
require_once('../core/services/base.php');
require_once('../core/neechy/config.php');
require_once('../test/helper.php');


class NeechyServiceTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
    }

    /**
     * Tests
     */
    public function testInstantiates() {
        $config = NeechyConfig::init();
        $service = new NeechyService($config);
        $this->assertInstanceOf('NeechyService', $service);
    }
}
