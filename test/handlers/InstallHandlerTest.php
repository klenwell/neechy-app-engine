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
require_once('../core/neechy/path.php');
require_once('../core/handlers/install/handler.php');


class InstallHandlerTest extends PHPUnit_Framework_TestCase {

    public $test_db_name = 'neechy_install_test';
    public $test_admin_name = 'InstallTestAdmin';

    /**
     * Test Fixtures
     */
    public function setUp() {
        NeechyTestHelper::setUp();
        UserFixture::init();
        PageFixture::init();
        $this->assertDatabaseDoesNotExist($this->test_db_name);

        $this->app_config_path = NeechyConfig::app_config_path();
        $this->assertDirDoesNotExist(
            $this->app_config_path,
            sprintf('App config file (%s) should not exist when running this test.',
                    $this->app_config_path)
        );
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $this->destroy_database_if_exists($this->test_db_name);
        $this->destroy_file_if_exists($this->app_config_path);
        $this->assertDatabaseDoesNotExist($this->test_db_name);
    }

    public function assertDatabaseExists($db_name) {
        $this->assertTrue((bool) $this->select_database($db_name));
    }

    public function assertDatabaseDoesNotExist($db_name) {
        $this->assertFalse((bool) $this->select_database($db_name));
    }

    public function assertDirDoesNotExist($dir_path, $message=null) {
        $this->assertFalse(file_exists($dir_path), $message);
    }

    public function assertUserExists($name) {
        $sql = 'SELECT name FROM users WHERE name = ?';
        $params = array($name);
        $query = $this->query($sql, $params);
        $this->assertEquals(1, $query->rowCount(), sprintf('User %s not found', $name));
    }

    public function assertPageExists($title) {
        $sql = 'SELECT title FROM pages WHERE title = ?';
        $params = array($title);
        $query = $this->query($sql, $params);
        $this->assertGreaterThanOrEqual(1, $query->rowCount(),
            sprintf('Page %s not found', $title));
    }

    private function query($sql, $params=array()) {
        $dsn = sprintf('mysql:host=%s;dbname=%s',
                       NeechyConfig::get('mysql_host'),
                       $this->test_db_name);
        $pdo = new PDO($dsn,
                       NeechyConfig::get('mysql_user'),
                       NeechyConfig::get('mysql_password'));

        $query = $pdo->prepare($sql);
        $query->execute($params);
        return $query;
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

    private function destroy_file_if_exists($path) {
        if ( file_exists($path) ) {
            unlink($path);
        };
    }

    /*
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
                            'println',
                            'read_page_body_from_template',
                            'prompt_user'
                          ))
                        ->getMock();

        # Mock handler methods
        $handler->expects($this->any())->method('preamble');
        $handler->expects($this->any())->method('println');
        $handler->expects($this->any())->method('read_page_body_from_template')
                ->will($this->returnValue('body cannot be null'));

        # Mock prompt user method.
        $mockedPromptUserValues = array(
            array('Enter database host', 'localhost', NeechyConfig::get('mysql_host')),
            array('Enter database user name', '', NeechyConfig::get('mysql_user')),
            array('Enter database user password', '', NeechyConfig::get('mysql_password')),
            array('Enter database name', 'neechy', $this->test_db_name),
            array('Please enter your new user name', '', $this->test_admin_name),
            array('Please enter your email', '',
                  sprintf('%s@neechy.com', $this->test_admin_name)),
        );
        $handler->expects($this->any())
                ->method('prompt_user')
                ->will($this->returnValueMap($mockedPromptUserValues));

        # Assert pre-exercise conditions
        $this->assertDatabaseDoesNotExist($this->test_db_name);

        # Exercise
        $handler->handle();

        # Verify
        $this->assertEquals($this->test_db_name, NeechyConfig::get('mysql_database'));
        $this->assertDatabaseExists($this->test_db_name);
        $this->assertUserExists(NEECHY_USER);
        $this->assertPageExists(NEECHY_USER);
        $this->assertUserExists($this->test_admin_name);
        $this->assertPageExists($this->test_admin_name);
    }

    public function testInstantiates() {
        $handler = new InstallHandler();
        $this->assertInstanceOf('InstallHandler', $handler);
    }
}
