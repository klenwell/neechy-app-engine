<?php
/**
 * test/handlers/InstallHandlerTest.php
 *
 * Usage (run from Neechy root dir):
 * > phpunit --bootstrap test/bootstrap.php handlers/InstallHandlerTest
 *
 */
require_once('../test/helper.php');
require_once('../test/fixtures/page.php');
require_once('../test/fixtures/user.php');
require_once('../core/handlers/install/handler.php');


class InstallHandlerTest extends PHPUnit_Framework_TestCase {

    public $test_db_name = 'neechy_install_test';

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
        PageFixture::init();
        $this->assertDatabaseDoesNotExist($this->test_db_name);
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $this->destroy_database_if_exists($this->test_db_name);
        $this->assertDatabaseDoesNotExist($this->test_db_name);
    }

    public function assertDatabaseExists($db_name) {
        $this->assertTrue((bool) $this->select_database($db_name));
    }

    public function assertDatabaseDoesNotExist($db_name) {
        $this->assertFalse((bool) $this->select_database($db_name));
    }

    public function assertSystemUserExists($handler) {
        $sql = 'SELECT name FROM users WHERE name = "NeechySystem"';

        $dsn = sprintf('mysql:host=%s;dbname=%s',
                       NeechyConfig::get('mysql_host'),
                       $this->test_db_name);
        $pdo = new PDO($dsn,
                       NeechyConfig::get('mysql_user'),
                       NeechyConfig::get('mysql_password'));

        $query = $pdo->prepare($sql);
        $query->execute();

        $this->assertEquals(1, $query->rowCount());
    }

    private function select_database($db_name) {
        $sql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?';

        $pdo = NeechyTestHelper::connect_to_database_host();
        $query = $pdo->prepare($sql);
        $query->execute(array($db_name));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    private function destroy_database_if_exists($db_name) {
        $pdo = NeechyTestHelper::connect_to_database_host();
        $pdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $db_name));
        return $pdo;
    }

    /**
     * Tests
     */
    public function testShouldHandleInstallation() {
        # Mock out handler prompt_user method.
        $request = new NeechyRequest();

        # Mock out handler
        $handler = $this->getMockBuilder('InstallHandler')
                        ->setConstructorArgs(array($request))
                        ->setMethods(array('handle'))
                        ->getMock();
        $handler->expects($this->any())->method('handle');
        $this->assertNull($handler->handle());
    }

    public function testShouldSetupDatabase() {
        # Mock out handler prompt_user method.
        $request = new NeechyRequest();

        # Mock out handler
        $handler = $this->getMockBuilder('InstallHandler')
                        ->setConstructorArgs(array($request))
                        ->setMethods(array(
                            'preamble',
                            'create_default_pages',
                            'create_admin_user',
                            'update_config_file',
                            'prompt_user',
                            'println'
                          ))
                        ->getMock();

        # Mock handler methods
        $handler->expects($this->any())->method('preamble');
        $handler->expects($this->any())->method('create_default_pages');
        $handler->expects($this->any())->method('create_admin_user');
        $handler->expects($this->any())->method('update_config_file');
        $handler->expects($this->any())->method('println');

        # Mock prompt user method.
        $mockedPromptUserValues = array(
            array('Enter database host', 'localhost', NeechyConfig::get('mysql_host')),
            array('Enter database user name', '', NeechyConfig::get('mysql_user')),
            array('Enter database user password', '', NeechyConfig::get('mysql_password')),
            array('Enter database name', 'neechy', $this->test_db_name)
        );
        $handler->expects($this->any())
                ->method('prompt_user')
                ->will($this->returnValueMap($mockedPromptUserValues));

        # Assert pre-exercise conditions
        $this->assertDatabaseDoesNotExist($this->test_db_name);

        # Exercise
        $handler->handle();

        # Verify
        $this->assertDatabaseExists($this->test_db_name);
        $this->assertSystemUserExists($handler);
    }

    public function testInstantiates() {
        $handler = new InstallHandler();
        $this->assertInstanceOf('InstallHandler', $handler);
    }
}
