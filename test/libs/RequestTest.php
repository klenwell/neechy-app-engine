<?php
/**
 * test/libs/RequestTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php libs/RequestTest
 *
 */
require_once('../core/libs/request.php');


class NeechyRequestTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        $this->request = new NeechyRequest();
    }

    public function tearDown() {
        $_SERVER = array();
        $this->request = null;
    }

    public function simulate_cgi_request($GET=NULL, $POST=NULL) {
        $_GET = is_array($GET) ? $GET : array();
        $_POST = is_array($POST) ? $POST : array();
    }

    /**
     * Tests
     */
    public function testParams() {
        $get_array = array(
            'page' => 'Home',
            'handler' => 'GET',
            'foo' => 'get'
        );
        $post_array = array(
            'handler' => 'POST',
            'action' => 'Save',
            'foo' => 'post'
        );

        $this->simulate_cgi_request($get_array, $post_array);
        $request = new NeechyRequest();

        $this->assertEquals('Home', $request->page);
        $this->assertEquals('post', $request->handler);
        $this->assertEquals('save', $request->action);
        $this->assertEquals('post', $request->param('foo'));
        $this->assertEquals('GET', $request->get('handler'));
        $this->assertEquals('POST', $request->post('handler'));
    }

    public function testRootUrlRequest() {
        $this->simulate_cgi_request();
        $request = new NeechyRequest();

        $this->assertEquals('Home', $request->page);
        $this->assertNull($request->handler);
        $this->assertNull($request->action);
    }

    public function testInstantiates() {
        $this->assertInstanceOf('NeechyRequest', $this->request);
    }
}
