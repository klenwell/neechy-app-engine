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
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function tearDown() {
        $_SERVER = array();
    }

    /**
     * Tests
     */
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
        $templater = new NeechyTemplater('no-theme');
        $this->assertInstanceOf('NeechyTemplater', $templater);
    }
}
