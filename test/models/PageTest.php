<?php
/**
 * test/models/PageTest.php
 *
 * Usage (run from root dir):
 * > phpunit --bootstrap test/bootstrap.php models/PageTest
 *
 */
require_once('../core/models/page.php');
require_once('../test/helper.php');


class PageModelTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
    }

    /**
     * Tests
     */
    public function testInstantiates() {
        $page = new Page();
        $this->assertInstanceOf('Page', $page);
        $this->assertInstanceOf('PDO', $page->pdo);
        $this->assertEquals('pages', $page->table);
    }
}
