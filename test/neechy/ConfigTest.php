<?php
/**
 * test/neechy/ConfigTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/ConfigTest
 *
 */
require_once('../core/neechy/config.php');


class NeechyConfigTest extends PHPUnit_Framework_TestCase {

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
    public function testInitAndGet() {
        NeechyConfig::init('../test/test.conf.php');
        $this->assertNull(NeechyConfig::get('unset-value'));
        $this->assertTrue(NeechyConfig::get('is-testing'));
        $this->assertEquals('NeechyTest', NeechyConfig::get('title'));
    }
}
