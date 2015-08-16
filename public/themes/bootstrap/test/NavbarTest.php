<?php
/**
 * public/themes/bootstrap/test/NavbarTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php ../public/themes/bootstrap/test/NavbarTest
 *
 */
require_once('../core/neechy/templater.php');


class BootstrapThemeNavbarTest extends PHPUnit_Framework_TestCase {

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
    public function testOk() {
        $this->assertTrue(true);
    }
}
