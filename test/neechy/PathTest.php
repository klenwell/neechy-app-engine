<?php
/**
 * test/neechy/PathTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/PathTest
 *
 */
require_once('../core/neechy/path.php');


class NeechyPathTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
    }

    public function tearDown() {
    }

    /**
     * Tests
     */
    public function testUrl() {
        $cases = array(
            # array(handler, action, params, expect)
            array(null, null, array(), '/'),
            array('handler', null, array(), '/handler/'),
            array(null, 'action', array(), '/action'),
            array('handler', 'action', array(), '/handler/action'),
            array('handler', 'action', array('p1', 'p2'), '/handler/action/p1/p2')
        );

        foreach ( $cases as $case ) {
            list($handler, $action, $params, $expected) = $case;
            $url = NeechyPath::url($handler, $action, $params);
            $this->assertEquals($expected, $url, sprintf('Case expecting %s failed', $expected));
        }
    }
}
