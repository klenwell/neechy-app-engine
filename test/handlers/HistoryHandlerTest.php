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

        $this->request = new NeechyRequest();
        $this->request->handler = 'history';
        $this->request->action = 'NeechyPage';
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $this->request = null;
        $this->page = null;
    }

    /**
     * Tests
     */
    public function testShouldReturnPageHistory() {
        $handler = new HistoryHandler($this->request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertContains('<td class="id"><a href="/history/NeechyPage/1">1</a></td>',
                              $response->body);
        $this->assertContains('<td class="id"><a href="/history/NeechyPage/2">2</a></td>',
                              $response->body);
        $this->assertContains('<td class="id"><a href="/history/NeechyPage/3">3</a></td>',
                              $response->body);
    }

    public function testInstantiates() {
        $handler = new HistoryHandler($this->request);
        $this->assertInstanceOf('HistoryHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
