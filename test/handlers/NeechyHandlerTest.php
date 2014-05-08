<?php
/**
 * test/handlers/NeechyHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/NeechyHandlerTest
 *
 */
require_once('../core/handlers/base.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');


class NeechyHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        PageFixture::init();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
    }

    /**
     * Tests
     */
    public function testInstantiates() {
        $handler = new NeechyHandler();
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
