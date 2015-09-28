<?php
/**
 * test/handlers/AuthHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/AuthHandlerTest
 *
 */
require_once('../core/handlers/auth/handler.php');
require_once('../core/neechy/request.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');


class AuthHandlerTest extends PHPUnit_Framework_TestCase {

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
    public function testShouldDisplayAuthView() {
        $request = new NeechyRequest();
        $page = Page::find_by_title('logout');

        $handler = new AuthHandler($request, $page);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertContains(sprintf('<button class="%s" type="submit">Sign in</button>',
                                      'btn btn-lg btn-primary btn-block'),
                              $response->body);
        $this->assertContains(sprintf('<button class="%s" type="submit">Sign up</button>',
                                      'btn btn-lg btn-primary btn-block'),
                              $response->body);
    }

    public function testInstantiates() {
        $request = new NeechyRequest();
        $handler = new AuthHandler($request);
        $this->assertInstanceOf('AuthHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
