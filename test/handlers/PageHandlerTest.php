<?php
/**
 * test/handlers/PageHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/PageHandlerTest
 *
 */
require_once('../core/handlers/page/handler.php');
require_once('../core/neechy/request.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');


class PageHandlerTest extends PHPUnit_Framework_TestCase {

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
        $request = new NeechyRequest();
        $handler = new PageHandler($request);
        $this->assertInstanceOf('PageHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
