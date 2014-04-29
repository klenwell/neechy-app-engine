<?php
/**
 * test/libs/ConfigTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit test/libs/ConfigTest
 *
 */
require_once('../core/libs/config.php');


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
