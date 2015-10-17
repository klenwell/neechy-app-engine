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


require_once 'google/appengine/testing/ApiCallArguments.php';
require_once 'google/appengine/testing/ApiProxyMock.php';



class AuthHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
        PageFixture::init();
        $_SESSION['csrf_token'] = 'foo';

        $this->mockCreateLoginUrl('index.php?page=HomePage');
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $_SESSION['csrf_token'] = null;
    }

    public function mockCreateLoginUrl($destination_url) {
        $this->apiProxyMock = new google\appengine\testing\ApiProxyMock();
        $this->apiProxyMock->init($this);

        $req = new \google\appengine\CreateLoginURLRequest();
        $req->setDestinationUrl($destination_url);
        $resp = new \google\appengine\CreateLoginURLResponse();
        $resp->setLoginUrl('http://www');

        $this->apiProxyMock->expectCall('user', 'CreateLoginURL', $req, $resp);
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
