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
    public function testFindByName() {
        $user = User::find_by_name('NeechyUser');
        $this->assertEquals('NeechyUser', $user->field('name'));
    }

    public function testSave() {
        $user = new User(array('name' => 'FreddyNeechy'));
        $query = $user->save();
        $this->assertEquals(1, $query->rowCount());
    }

    public function testInstantiates() {
        $user = new User();
        $this->assertInstanceOf('User', $user);
        $this->assertEquals('users', $user->table);
    }
}
