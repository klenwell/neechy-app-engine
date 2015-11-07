<?php
/**
 * test/neechy/HelperTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/HelperTest
 *
 */
require_once('../core/neechy/helper.php');


class NeechyHelperTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function tearDown() {
        $_SERVER = array();
    }

    /**
     * Tests
     */
    public function testHandlerLink() {
        $link = NeechyHelper::handler_link('HomePage');
        $this->assertEquals('<a href="/">HomePage</a>', $link);

        $link = NeechyHelper::handler_link(
            'label',
            'handler',
            'action',
            array(
              'title' => 'title',
              'class' => 'class',
              'id' => 'id'
        ));
        $expect = '<a href="/handler/action" title="title" class="class" id="id">label</a>';
        $this->assertEquals($expect, $link);
    }

    public function testLink() {
        $link = NeechyHelper::link('http://github.com/', 'github');
        $this->assertEquals('<a href="http://github.com/">github</a>', $link);

        $link = NeechyHelper::link('/', 'home', array(
            'title' => 'go home',
            'class' => 'link',
            'id' => 'home-link'
        ));
        $expect = '<a href="/" title="go home" class="link" id="home-link">home</a>';
        $this->assertEquals($expect, $link);
    }

    public function testInstantiates() {
        $helper = new NeechyHelper();
        $this->assertInstanceOf('NeechyHelper', $helper);
    }
}
