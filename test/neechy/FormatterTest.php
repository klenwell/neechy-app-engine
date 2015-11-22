<?php
/**
 * test/neechy/FormatterTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php neechy/FormatterTest
 *
 */
require_once('../core/neechy/formatter.php');


class FormatterTest extends PHPUnit_Framework_TestCase {

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
    public function testWikkaLinkTranslation() {
    }

    public function testInstantiates() {
        $formatter = new NeechyFormatter();
        $this->assertInstanceOf('NeechyFormatter', $formatter);
    }
}
