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
            array(array('MyPage', NULL, NULL), '?page=MyPage'),
            array(array('MyPage', 'handler', NULL), '?page=MyPage&handler=handler'),
            array(array('MyPage', NULL, 'action'), '?page=MyPage&action=action'),
            array(array('MyPage', 'handler', 'action'),
                '?page=MyPage&handler=handler&action=action'),
        );

        foreach ( $cases as $case ) {
            list($args, $expected) = $case;
            list($page, $handler, $action) = $args;
            $url = NeechyPath::url($page, $handler, $action);
            $this->assertEquals($expected, $url);
        }
    }
}

