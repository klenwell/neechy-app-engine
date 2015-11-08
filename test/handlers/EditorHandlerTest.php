<?php
/**
 * test/handlers/EditorHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/EditorHandlerTest
 *
 */
require_once('../core/handlers/editor/handler.php');
require_once('../core/neechy/request.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');


class EditorHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
        PageFixture::init();
        $_SESSION['csrf_token'] = 'foo';
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $_SESSION['csrf_token'] = null;
    }

    /**
     * Tests
     */
    public function testShouldDisplayEditor() {
        $request = new NeechyRequest();
        $request->handler = 'edit';
        $request->action = 'NeechyPage';
        $page = Page::find_by_title('NeechyPage');

        $handler = new EditorHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertContains('<div id="wmd-editor" class="wmd-panel">',
                              $response->body);
        $this->assertContains($page->field('body'), $response->body);
    }

    public function testShouldDisplayPreview() {
        $_POST['purpose'] = 'preview';
        $_POST['wmd-input'] = '**Bold** and *italics*';

        $request = new NeechyRequest();
        $request->handler = 'edit';
        $request->action = 'NeechyPage';
        $page = Page::find_by_title('NeechyPage');

        $handler = new EditorHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertContains('<div id="wmd-preview" class="wmd-panel wmd-preview well">',
                              $response->body);
        $this->assertContains('<strong>Bold</strong> and <em>italics</em>', $response->body);
    }

    public function testShouldRedisplayEditorWhenEditButtonPushed() {
        $page = Page::find_by_title('NeechyPage');

        # Edit action requires hidden textarea.
        $_POST['purpose'] = 'edit';
        $_POST['wmd-input'] = $page->field('body');

        # POST vars must be set before NeechyRequest called.
        $request = new NeechyRequest();
        $request->handler = 'edit';
        $request->action = 'NeechyPage';

        $handler = new EditorHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertContains('<div id="wmd-editor" class="wmd-panel">',
                              $response->body);
        $this->assertContains($page->field('body'), $response->body);
    }

    public function testInstantiates() {
        $request = new NeechyRequest();
        $handler = new EditorHandler($request);
        $this->assertInstanceOf('EditorHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
