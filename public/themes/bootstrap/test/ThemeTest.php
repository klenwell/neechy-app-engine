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
        $this->assertContains($needle, $html);
    }

    public function testShouldShowUserButtonInNavbar() {
        $user = User::find_by_name('NeechyUser');
        $user->login();
        $html = $this->renderTemplate();

        $needle = '<div class="btn btn-group user-button logged-in">';
        $this->assertContains($needle, $html);
    }
}
