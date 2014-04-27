<?php
/**
 * test/libs/TemplaterTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit test/libs/TemplaterTest
 *
 */
require_once('../core/libs/templater.php');


class NeechyTemplaterTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        $this->templater = new NeechyTemplater();
    }

    public function tearDown() {
        $_SERVER = array();
        $this->templater = null;
    }

    /**
     * Tests
     */
    public function testInstantiates() {
        $this->assertInstanceOf('NeechyTemplater', $this->templater);
    }
}
