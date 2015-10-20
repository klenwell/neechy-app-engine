<?php
/**
 * test/handlers/ErrorHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/ErrorHandlerTest
 *
 */
require_once('../core/handlers/error/handler.php');
require_once('../core/neechy/request.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');


class ErrorHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
        PageFixture::init();
        $_SESSION['csrf_token'] = 'foo';
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $_SESSION['csrf_token'] = null;
    }

    /**
     * Tests
     */
    public function testShouldDisplay404Error() {
        $request = new NeechyRequest();
        $page = Page::find_by_title('NeechyPage');

        try {
           throw new NeechyWebServiceError('Testing 404 Error', 404);
        }
        catch (NeechyError $e) {
            $handler = new ErrorHandler($request);
            $response = $handler->handle_error($e);
        }

        $this->assertEquals(404, $response->status);
        $this->assertContains($e->getMessage(), $response->body);
        $this->assertContains('Testing 404 Error', $response->body);
    }

    public function testInstantiates() {
        $request = new NeechyRequest();
        $handler = new ErrorHandler($request);
        $this->assertInstanceOf('ErrorHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
