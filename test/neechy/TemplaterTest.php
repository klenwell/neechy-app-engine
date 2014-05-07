<?php
/**
 * test/neechy/TemplaterTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/TemplaterTest
 *
 */
require_once('../core/neechy/templater.php');


class NeechyTemplaterTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        $this->templater = new NeechyTemplater();
    }

    public function tearDown() {
        $_SERVER = array();
        $this->templater = null;
    }

    /**
     * Tests
     */
    public function testNeechyLink() {
        $link = $this->templater->neechy_link('HomePage');
        $this->assertEquals('<a href="?page=HomePage">HomePage</a>', $link);

        $link = $this->templater->neechy_link('label',
            'HomePage',
            'handler',
            'action',
            array(
              'title' => 'home',
              'class' => 'link',
              'id' => 'home-link'
        ));
        $expect = '<a href="?page=HomePage&handler=handler&action=action" ' .
            'title="home" class="link" id="home-link">label</a>';
        $this->assertEquals($expect, $link);
    }

    public function testLink() {
        $link = $this->templater->link('http://github.com/', 'github');
        $this->assertEquals('<a href="http://github.com/">github</a>', $link);

        $link = $this->templater->link('/', 'home', array(
            'title' => 'go home',
            'class' => 'link',
            'id' => 'home-link'
        ));
        $expect = '<a href="/" title="go home" class="link" id="home-link">home</a>';
        $this->assertEquals($expect, $link);
    }

    public function testSetLayoutTokens() {
        $tokens = array(
            'head' => '<title>Neechy</title>',
            'top' => '<h1>Neechy</h1>',
            'middle' => 'middle',
            'bottom' => '<footer>bottom</footer>'
        );

        $templater = new NeechyTemplater('no-theme');

        foreach ( $tokens as $token => $content ) {
            $templater->set($token, $content);
        }

        $output = $templater->render();

        foreach ( $tokens as $token => $content ) {
            $this->assertContains($content, $output);
        }
    }

    public function testInstantiates() {
        $this->assertInstanceOf('NeechyTemplater', $this->templater);
    }
}
