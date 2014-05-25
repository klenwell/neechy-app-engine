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
    public function testSave() {
        $title = 'NewPage';

        $page = Page::find_by_title($title);
        $page->set('body', 'a new page');
        $page->set('note', 'testSave');
        $this->assertTrue($page->is_new());
        $this->assertEquals($page->field('slug'), 'newpage');

        $page->save();
        $this->assertFalse($page->is_new());

        $page->set('note', 'updating page');
        $page->save();
        $page->load_history();
        $this->assertEquals(2, count($page->edits));
    }

    public function testUrl() {
        $page = Page::find_by_title('NeechyPage');
        $this->assertEquals('?page=neechypage', $page->url());
    }

    public function testFindByTitle() {
        $page = Page::find_by_title('NewPage');   # page does not exist yet
        $this->assertEquals('NewPage', $page->field('title'));
        $this->assertTrue($page->is_new());

        $page = Page::find_by_title('NeechyPage');
        $this->assertEquals('NeechyPage', $page->field('title'));
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
