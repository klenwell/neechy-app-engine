<?php
/**
 * test/handlers/PageHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/PreferencesHandlerTest
 *
 */
require_once('../core/handlers/preferences/handler.php');
require_once('../core/neechy/request.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');


class PreferencesHandlerTest extends PHPUnit_Framework_TestCase {

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

    public function loginUser() {
        $user = User::find_by_name('NeechyUser');
        $user->set_password('foo');
        $user->save();
        $user->login();
        return $user;
    }

    public function assertPasswordUnchanged($user_name, $old_password) {
        $user = User::find_by_name($user_name);
        $new_password = $user->field('password');
        $this->assertEquals($old_password, $new_password);
    }

    /**
     * Tests
     */
    public function testShouldUpdatePassword() {
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordFieldIsMissing() {
        $user = $this->loginUser();
        $old_password = $user->field('password');

        $request = new NeechyRequest();
        $request->action = 'change-password';
        $_POST['old-password'] = null;
        $_POST['new-password'] = 'neechy123';
        $_POST['new-password-confirm'] = 'neechy123';

        $handler = new PreferencesHandler($request);
        $content = $handler->handle();
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = '<span class="help-block">Password required.</span>';
        $this->assertContains($needle, $content);
    }

    public function testShouldNotUpdatePasswordWhenCurrentPasswordFieldIsIncorrect() {
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordConfirmationDoesNotMatch() {
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordIsUserName() {
    }

    public function testShouldRequirePasswordPresent() {

    }

    public function testInstantiates() {
        $request = new NeechyRequest();
        $handler = new PreferencesHandler($request);
        $this->assertInstanceOf('PreferencesHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
