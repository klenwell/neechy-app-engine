<?php
/**
 * test/libs/TemplaterTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit test/libs/TemplaterTest
 *
 */
require_once('../core/libs/templater.php');


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
    public function testRenderWithPartialArray() {
        $this->templater->partial['head'] = '<head><title>Neechy</title></head>';
        $this->templater->partial['top'] = '<h1>Neechy</h1>';
        $this->templater->partial['middle'] = 'middle';
        $this->templater->partial['bottom'] = '<footer>bottom</footer>';

        $output = $this->templater->render();

        foreach ( $this->templater->partial as $partial => $content ) {
            $this->assertContains($content, $output);
        }
    }

    public function testInstantiates() {
        $this->assertInstanceOf('NeechyTemplater', $this->templater);
    }
}
