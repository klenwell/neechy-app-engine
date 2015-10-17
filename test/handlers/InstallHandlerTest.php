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
require_once('../core/neechy/config.php');
require_once('../core/handlers/install/handler.php');


class InstallHandlerTestError extends Exception {}


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
    }

    public function tearDown() {
        NeechyTestHelper::tearDown();
        $this->destroy_database_if_exists($this->test_db_name);
        $this->destroy_file_if_exists(NeechyConfig::app_config_path());
        $this->assertDatabaseDoesNotExist($this->test_db_name);
    }

    public function onNotSuccessfulTest($e){
        $this->destroy_file_if_exists(NeechyConfig::app_config_path());
        parent::onNotSuccessfulTest($e);
    }

    public function assertDatabaseExists($db_name) {
        $this->assertTrue((bool) $this->select_database($db_name));
    }

    public function assertDatabaseDoesNotExist($db_name) {
        $this->assertFalse((bool) $this->select_database($db_name));
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

    private function mock_install_handler($mocked_methods) {
        # Mock out handler prompt_user method.
        $request = new NeechyRequest();

        # Mock out handler
        $handler = $this->getMockBuilder('InstallHandler')
                        ->setConstructorArgs(array($request))
                        ->setMethods($mocked_methods)
                        ->getMock();

        return $handler;
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

        $pdo = NeechyDatabase::connect_to_database_host();
        $query = $pdo->prepare($sql);
        $query->execute(array($db_name));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    private function destroy_database_if_exists($db_name) {
        $pdo = NeechyDatabase::connect_to_database_host();
        $pdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $db_name));
        return $pdo;
    }

    private function destroy_file_if_exists($path) {
        # Disable this for app-engine version
        return;

        if ( file_exists($path) ) {
            unlink($path);
        };
    }

    /*
     * Tests
     */
    public function testHandlerFromWeb() {
        $request = new NeechyRequest();
        $handler = new InstallHandler($request);
        $handler->is_console = false;
        $content = $handler->handle();
        $this->assertEquals('<h4>Install runs from the console.</h4>', $content);
    }

    public function testInstantiates() {
        $handler = new InstallHandler();
        $this->assertInstanceOf('InstallHandler', $handler);
    }
}
