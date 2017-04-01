<?php
/**
 * public/themes/bootstrap/test/ThemeTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php ../public/themes/bootstrap/test/ThemeTest
 *
 */
require_once('../test/helper.php');
require_once('../test/fixtures/user.php');
require_once('../core/neechy/templater.php');


class BootstrapThemeTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $this->html = null;
    }

    public function renderTemplate() {
        $templater = NeechyTemplater::load();
        $templater->page = 'NeechyPage';
        $templater->set('content', '<h1>Test</h1>');
        return $templater->render();
    }

    /**
     * Tests
     */
    public function testShouldShowLoginButtonInNavbar() {
        $needle = '<div class="user-button">';
        $html = $this->renderTemplate();
        $this->assertContains('<!-- Bootstrap Navbar -->', $html);
        $this->assertContains($needle, $html);
    }

    public function testShouldShowUserButtonInNavbar() {
        $needle = '<div class="btn btn-group user-button logged-in">';

        $user = User::find_by_name('NeechyUser');
        $user->login();
        $this->assertTrue(User::is_logged_in());

        $html = $this->renderTemplate();
        $this->assertContains('<!-- Bootstrap Navbar -->', $html);
        $this->assertContains($needle, $html);
    }

    public function testShouldRenderSetContent() {
        $content = '<p>I should be found in rendered html.</p>';
        $templater = NeechyTemplater::load();
        $templater->page = 'NeechyPage';
        $templater->set('content', $content);
        $html = $templater->render();
        $this->assertContains($content, $html);
    }
}
