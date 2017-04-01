<?php
/**
 * test/handlers/PasswordHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/PasswordHandlerTest
 *
 */
require_once('../core/handlers/password/handler.php');
require_once('../core/neechy/request.php');
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');


class PasswordHandlerTest extends PHPUnit_Framework_TestCase {

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
        $handler = $this->getMockBuilder('PasswordHandler')
                        ->setConstructorArgs(array($request))
                        ->setMethods(array('redirect'))
                        ->getMock();

        $handler->expects($this->any())
                ->method('redirect')
                ->will($this->returnValue('redirected'));

        $this->assertNull(User::current());
        $redirected = $handler->handle();
        $this->assertEquals('redirected', $redirected);
    }

    public function testShouldUpdatePassword() {
        $_POST['old-password'] = 'password';
        $_POST['new-password'] = sprintf('%supdated', $_POST['old-password']);
        $_POST['new-password-confirm'] = $_POST['new-password'];
        $_POST['purpose'] = 'change-password';

        $user = $this->loginUser($_POST['old-password']);
        $old_password = $user->field('password');

        $request = new NeechyRequest();

        $handler = new PasswordHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertPasswordChanged($user->field('name'), $old_password);
        $this->assertContains('Your password has been changed.', $response->body);
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordFieldIsMissing() {
        $_POST['old-password'] = null;
        $_POST['new-password'] = 'neechy123';
        $_POST['new-password-confirm'] = $_POST['new-password'];
        $_POST['purpose'] = 'change-password';

        $user = $this->loginUser('password');
        $old_password = $user->field('password');

        $request = new NeechyRequest();

        $handler = new PasswordHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = '<span class="help-block">Password required.</span>';
        $this->assertContains($needle, $response->body);
    }

    public function testShouldNotUpdatePasswordWhenCurrentPasswordFieldIsIncorrect() {
        $_POST['old-password'] = 'password';
        $_POST['new-password'] = sprintf('%supdated', $_POST['old-password']);
        $_POST['new-password-confirm'] = $_POST['new-password'];
        $_POST['purpose'] = 'change-password';

        $user = $this->loginUser('wrong-password');
        $old_password = $user->field('password');

        $request = new NeechyRequest();

        $handler = new PasswordHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = '<span class="help-block">Password is incorrect. Please try again.</span>';
        $this->assertContains($needle, $response->body);
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordConfirmationDoesNotMatch() {
        $_POST['old-password'] = 'password';
        $_POST['new-password'] = sprintf('%supdated', $_POST['old-password']);
        $_POST['new-password-confirm'] = 'does-not-match-new-password';
        $_POST['purpose'] = 'change-password';

        $user = $this->loginUser($_POST['old-password']);
        $old_password = $user->field('password');

        $request = new NeechyRequest();

        $handler = new PasswordHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = sprintf('<span class="help-block">%s</span>',
                          'Password fields do not match. Please try again.');
        $this->assertContains($needle, $response->body);
    }

    public function testShouldNotUpdatePasswordWhenNewPasswordIsUserName() {
        $_POST['old-password'] = 'password';
        $user = $this->loginUser($_POST['old-password']);

        $_POST['new-password'] = $user->field('name');
        $_POST['new-password-confirm'] = $user->field('name');
        $_POST['purpose'] = 'change-password';

        $old_password = $user->field('password');

        $request = new NeechyRequest();

        $handler = new PasswordHandler($request);
        $response = $handler->handle();

        $this->assertEquals(200, $response->status);
        $this->assertPasswordUnchanged($user->field('name'), $old_password);

        $needle = sprintf('<span class="help-block">%s</span>',
                          'User name and password should not match.');
        $this->assertContains($needle, $response->body);
    }

    public function testInstantiates() {
        $request = new NeechyRequest();
        $handler = new PasswordHandler($request);
        $this->assertInstanceOf('PasswordHandler', $handler);
        $this->assertInstanceOf('NeechyHandler', $handler);
    }
}
