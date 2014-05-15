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
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');


class PageModelTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
        PageFixture::init();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
    }

    /**
     * Tests
     */
    public function testUrl() {
        $page = Page::find_by_tag('NeechyPage');
        $this->assertEquals('?page=NeechyPage', $page->url());
    }

    public function testFindByTag() {
        $page = Page::find_by_tag('NewPage');   # page does not exist yet
        $this->assertEquals('NewPage', $page->field('tag'));
        $this->assertTrue($page->is_new());

        $page = Page::find_by_tag('NeechyPage');
        $this->assertEquals('NeechyPage', $page->field('tag'));
        $this->assertEquals('version 3', $page->field('note'));
        $this->assertEquals('version 1', $page->primogenitor->field('note'));
        $this->assertFalse($page->is_new());
    }

    public function testInstantiates() {
        $page = new Page();
        $this->assertInstanceOf('Page', $page);
        $this->assertInstanceOf('PDO', $page->pdo);
        $this->assertEquals('pages', $page->table);
    }
}
