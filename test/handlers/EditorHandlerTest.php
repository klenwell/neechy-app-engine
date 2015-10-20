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

        $this->mockCreateLoginUrl('index.php?page=HomePage');
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $_SESSION['csrf_token'] = null;
    }

    public function mockCreateLoginUrl($destination_url) {
        $this->apiProxyMock = new google\appengine\testing\ApiProxyMock();
        $this->apiProxyMock->init($this);

        $req = new \google\appengine\CreateLoginURLRequest();
        $req->setDestinationUrl($destination_url);
        $resp = new \google\appengine\CreateLoginURLResponse();
        $resp->setLoginUrl('http://www');

        $this->apiProxyMock->expectCall('user', 'CreateLoginURL', $req, $resp);
    }

    /**
     * Tests
     */
    public function testShouldDisplayEditor() {
        $request = new NeechyRequest();
        $request->page = 'NeechyPage';
        $page = Page::find_by_title('NeechyPage');

        $handler = new EditorHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertContains('<div id="wmd-editor" class="wmd-panel">',
                              $response->body);
        $this->assertContains($page->field('body'), $response->body);
    }

    public function testShouldDisplayPreview() {
        $_GET['action'] = 'preview';
        $_POST['wmd-input'] = '**Bold** and *italics*';

        $request = new NeechyRequest();
        $page = Page::find_by_title('NeechyPage');

        $handler = new EditorHandler($request, $page);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertContains('<div id="wmd-preview" class="wmd-panel wmd-preview well">',
                              $response->body);
        $this->assertContains('<strong>Bold</strong> and <em>italics</em>', $response->body);
    }

    public function testShouldRedisplayEditorWhenEditButtonPushed() {
        $page = Page::find_by_title('NeechyPage');

        # Edit action requires hidden textarea.
        $_POST['action'] = 'edit';
        $_POST['wmd-input'] = $page->field('body');

        # POST vars must be set before NeechyRequest called.
        $request = new NeechyRequest();

        $handler = new EditorHandler($request, $page);
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
