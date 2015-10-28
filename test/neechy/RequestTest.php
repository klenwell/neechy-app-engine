<?php
/**
 * test/neechy/RequestTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/RequestTest
 *
 */
require_once('../core/neechy/request.php');


class NeechyRequestTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        $_SERVER['REQUEST_URI'] = '/';
        $this->request = new NeechyRequest();
    }

    public function tearDown() {
        $_SERVER = array();
        $this->request = null;
    }

    public function simulate_cgi_request($GET=null, $POST=null) {
        $_GET = is_array($GET) ? $GET : array();
        $_POST = is_array($POST) ? $POST : array();
    }

    /**
     * Tests
     */
    public function testParams() {
        $get_array = array(
            'param1' => 'one',
            'param2' => 'two'
        );
        $post_array = array(
            'purpose' => 'testing',
            'param1' => 'one',
            'param2' => 'two'
        );

        $_SERVER['REQUEST_URI'] = '/request/test/param1/param2';
        $this->simulate_cgi_request($get_array, $post_array);
        $request = new NeechyRequest();

        # Friendly URL: /handler/actions/params...
        $this->assertEquals('request', $request->handler);
        $this->assertEquals('test', $request->action);
        $this->assertEquals('param1', $request->param(0));
        $this->assertEquals('param2', $request->param(1));
        $this->assertNull($request->param(2));

        # GET/Query Params
        $this->assertEquals('one', $request->query('param1'));
        $this->assertEquals('two', $request->query('param2'));
        $this->assertNull($request->query('param3'));
        $this->assertEquals('one', $request->get('param1'));
        $this->assertEquals('two', $request->get('param2'));
        $this->assertNull($request->get('param3'));

        # POST Params
        $this->assertEquals('testing', $request->post('purpose'));
        $this->assertEquals('one', $request->post('param1'));
        $this->assertEquals('two', $request->post('param2'));
        $this->assertNull($request->post('param3'));
    }

    public function testShouldParseFriendlyUrlIntoHandlerAndAction() {
        $_SERVER['REQUEST_URI'] = '/request/test';
        $this->simulate_cgi_request();
        $request = new NeechyRequest();

        $this->assertEquals('request', $request->handler);
        $this->assertEquals('test', $request->action);
    }

    public function testInstantiates() {
        $this->assertInstanceOf('NeechyRequest', $this->request);
    }
}
