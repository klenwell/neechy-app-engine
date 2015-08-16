<?php
/**
 * public/themes/bootstrap/test/NavbarTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php ../public/themes/bootstrap/test/NavbarTest
 *
 */
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');
require_once('../core/neechy/templater.php');


class BootstrapThemeNavbarTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        $templater = NeechyTemplater::load();
        $templater->page = 'NeechyPage';
        $templater->set('content', '<h1>Test</h1>');
        $this->html = $templater->render();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $this->html = null;
    }

    /**
     * Tests
     */
    public function testNavbar() {
        echo $this->html;
    }
}
