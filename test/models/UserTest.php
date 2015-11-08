<?php
/**
 * test/models/UserTest.php
 *
 * Usage (run from root dir):
 * > phpunit --bootstrap test/bootstrap.php models/UserTest
 *
 */
require_once('../core/models/user.php');
require_once('../test/helper.php');
require_once('../test/fixtures/user.php');


class UserModelTest extends PHPUnit_Framework_TestCase {

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
    }

    /**
     * Tests
     */
    public function testShouldFindUserByName() {
        $user = User::find_by_name('NeechyUser');
        $this->assertEquals('NeechyUser', $user->field('name'));
    }

    public function testShouldSaveUser() {
        $user = new User(array('name' => 'FreddyNeechy'));
        $user->save();
        $this->assertEquals(1, $user->rows_affected);
    }

    public function testShouldRegisterNewUser() {
        $alice = User::register('AliceInWonderland', 'alice@wonderland.org', 'jabberw0cky');
        $this->assertInstanceOf('User', $alice);
        $this->assertEquals('AliceInWonderland', $alice->field('name'));
    }

    public function testShouldLogInAndLogOutUser() {
        $user = User::find_by_name('NeechyUser');
        $user->login();
        $this->assertTrue(User::is_logged_in($user), 'User should be logged in.');

        $user->logout();
        $this->assertFalse(User::is_logged_in($user), 'User should be logged out.');
    }

    public function testShouldReturnLoggedInUser() {
        $user = User::find_by_name('NeechyUser');
        $user->login();

        $this->assertInstanceOf('User', User::current());
        $this->assertEquals($user->field('name'), User::current('name'));
        $this->assertEquals($user->field('name'), User::current()->field('name'));
    }

    public function testShouldReturnUrlForUserPage() {
        $user = User::find_by_name('NeechyUser');
        $this->assertEquals('/page/NeechyUser', $user->url());
    }

    public function testShouldSetSecureUserPassword() {
        $user = User::find_by_name('NeechyUser');
        $password = 'password';

        $user->set_password($password);
        $this->assertTrue(NeechySecurity::verify_password($password,
                                                          $user->field('password')));
    }

    public function testShouldConvertFieldsToJSON() {
        $user = User::find_by_name('NeechyUser');
        $expected = sprintf(
            '{"id":"1","name":"NeechyUser","email":"nuser@neechy.org","password":"",' .
            '"status":"0","challenge":"","theme":"","show_comments":"N",' .
            '"created_at":"%s","updated_at":null}',
            $user->field('created_at')
        );
        $this->assertEquals($expected, $user->to_json());
    }

    public function testShouldInstantiateUser() {
        $user = new User();
        $this->assertInstanceOf('User', $user);
        $this->assertEquals('users', $user->table);
    }
}
