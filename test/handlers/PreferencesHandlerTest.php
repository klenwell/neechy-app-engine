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

    public function loginUser($password) {
        $user = User::find_by_name('NeechyUser');
        $user->set_password($password);
        $user->save();
        $user->login();
        return $user;
    }

    public function assertPasswordChanged($user_name, $old_password) {
        $user = User::find_by_name($user_name);
        $new_password = $user->field('password');
        $this->assertNotEquals($old_password, $new_password);
    }

    public function assertPasswordUnchanged($user_name, $old_password) {
        $user = User::find_by_name($user_name);
        $new_password = $user->field('password');
        $this->assertEquals($old_password, $new_password);
    }

    /**
     * Tests
     */
    public function testShouldRedirectUserWhenNotLoggedIn() {
        $request = new NeechyRequest();

        # Mock out redirect function (note: 3.7 syntax)
        $handler = $this->getMockBuilder('PreferencesHandler')
                        ->setConstructorArgs(array($request))
                        ->setMethods(array('redirect'))
                        ->getMock();

        $handler->expects($this->any())
                ->method('redirect')
                ->will($this->returnValue('redirected'));

        $this->assertNull(User::logged_in());
        $redirected = $handler->handle();
        $this->assertEquals('redirected', $redirected);
    }

    public function testShouldUpdatePassword() {
        $_POST['old-password'] = 'password';
        $_POST['new-password'] = sprintf('%supdated', $_POST['old-password']);
        $_POST['new-password-confirm'] = $_POST['new-password'];

        $user = $this->loginUser($_POST['old-password']);
        $old_password = $user->field('password');

        $request = new NeechyRequest();
        $request->action = 'change-password';

        $handler = new PreferencesHandler($request);
        $content = $handler->handle();
        $this->assertPasswordChanged($user->field('name'), $old_password);
        $this->assertEquals('Your password has been changed.',
                            $_SESSION['neechy-flash']['success'][0]);
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordFieldIsMissing() {
        $_POST['old-password'] = null;
        $_POST['new-password'] = 'neechy123';
        $_POST['new-password-confirm'] = $_POST['new-password'];

        $user = $this->loginUser('password');
        $old_password = $user->field('password');

        $request = new NeechyRequest();
        $request->action = 'change-password';

        $handler = new PreferencesHandler($request);
        $content = $handler->handle();
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = '<span class="help-block">Password required.</span>';
        $this->assertContains($needle, $content);
    }

    public function testShouldNotUpdatePasswordWhenCurrentPasswordFieldIsIncorrect() {
        $_POST['old-password'] = 'password';
        $_POST['new-password'] = sprintf('%supdated', $_POST['old-password']);
        $_POST['new-password-confirm'] = $_POST['new-password'];

        $user = $this->loginUser('wrong-password');
        $old_password = $user->field('password');

        $request = new NeechyRequest();
        $request->action = 'change-password';

        $handler = new PreferencesHandler($request);
        $content = $handler->handle();
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = '<span class="help-block">Password is incorrect. Please try again.</span>';
        $this->assertContains($needle, $content);
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordConfirmationDoesNotMatch() {
        $_POST['old-password'] = 'password';
        $_POST['new-password'] = sprintf('%supdated', $_POST['old-password']);
        $_POST['new-password-confirm'] = 'does-not-match-new-password';

        $user = $this->loginUser($_POST['old-password']);
        $old_password = $user->field('password');

        $request = new NeechyRequest();
        $request->action = 'change-password';

        $handler = new PreferencesHandler($request);
        $content = $handler->handle();
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = sprintf('<span class="help-block">%s</span>',
                          'Password fields do not match. Please try again.');
        $this->assertContains($needle, $content);
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordIsUserName() {
        $_POST['old-password'] = 'password';
        $user = $this->loginUser($_POST['old-password']);

        $_POST['new-password'] = $user->field('name');
        $_POST['new-password-confirm'] = $user->field('name');

        $old_password = $user->field('password');

        $request = new NeechyRequest();
        $request->action = 'change-password';

        $handler = new PreferencesHandler($request);
        $content = $handler->handle();
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = sprintf('<span class="help-block">%s</span>',
                          'User name and password should not match.');
        $this->assertContains($needle, $content);
    }

    public function testInstantiates() {
        $request = new NeechyRequest();
        $handler = new PreferencesHandler($request);
        $this->assertInstanceOf('PreferencesHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
