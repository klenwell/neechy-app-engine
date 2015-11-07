<?php
/**
 * test/handlers/HistoryHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/HistoryHandlerTest
 *
 */
require_once('../core/handlers/history/handler.php');
require_once('../core/neechy/request.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');


class HistoryHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
        PageFixture::init();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
    }

    /**
     * Tests
     */
    public function testShouldReturnPageHistory() {
        $_SERVER['REQUEST_URI'] = '/history/neechypage';
        $request = new NeechyRequest();
        $handler = new HistoryHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertContains('<td class="id"><a href="/history/neechypage/1">1</a></td>',
                              $response->body);
        $this->assertContains('<td class="id"><a href="/history/neechypage/2">2</a></td>',
                              $response->body);
        $this->assertContains('<td class="id"><a href="/history/neechypage/3">3</a></td>',
                              $response->body);
    }

    public function testShouldShowHistoricalVersion() {
        $_SERVER['REQUEST_URI'] = '/history/neechypage/1';
        $request = new NeechyRequest();
        $handler = new HistoryHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
    }

    public function testInstantiates() {
        $request = new NeechyRequest();
        $request->handler = 'history';
        $handler = new HistoryHandler($request);
        $this->assertInstanceOf('HistoryHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
