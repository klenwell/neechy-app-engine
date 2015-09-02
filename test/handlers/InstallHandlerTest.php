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
        if ( file_exists($path) ) {
            unlink($path);
        };
    }

    /*
     * Tests
     */
    public function testShouldReloadConfigSettingsInputByUser() {
        # Mock handler
        $mocked_methods = array('preamble',
                                'create_model_tables',
                                'create_neechy_user',
                                'create_default_pages',
                                'create_admin_user',
                                'println',
                                'prompt_user');
        $handler = $this->mock_install_handler($mocked_methods);
        $handler->is_console = true;

        # Mock handler methods
        $handler->expects($this->any())->method('preamble');
        $handler->expects($this->any())->method('create_model_tables');
        $handler->expects($this->any())->method('create_neechy_user');
        $handler->expects($this->any())->method('create_default_pages');
        $handler->expects($this->any())->method('create_admin_user');
        $handler->expects($this->any())->method('println');

        # Mock prompt user method.
        $mockedPromptUserValues = array(
            array('Enter database host', 'localhost', NeechyConfig::get('mysql_host')),
            array('Enter database user name', '', NeechyConfig::get('mysql_user')),
            array('Enter database user password', '', NeechyConfig::get('mysql_password')),
            array('Enter database name', 'neechy', $this->test_db_name),
        );
        $handler->expects($this->any())
                ->method('prompt_user')
                ->will($this->returnValueMap($mockedPromptUserValues));

        # Assert pre-exercise conditions
        $this->assertDatabaseDoesNotExist($this->test_db_name);
        $this->assertEquals('test', NeechyConfig::environment());

        # Exercise
        $handler->handle();

        # Verify
        $this->assertTrue($handler->is_console);
        $this->assertEquals($this->test_db_name, NeechyConfig::get('mysql_database'));
        $this->assertDatabaseExists($this->test_db_name);
        $this->assertEquals('app', NeechyConfig::environment());
    }

    public function testShouldSuccessfullySetupNeechyFromConsole() {
        # Mock handler
        $mocked_methods = array('preamble',
                                'println',
                                'read_page_body_from_template',
                                'prompt_user');
        $handler = $this->mock_install_handler($mocked_methods);
        $handler->is_console = true;

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
        $this->assertTrue($handler->is_console);
        $this->assertEquals('app', NeechyConfig::environment());
        $this->assertEquals($this->test_db_name, NeechyConfig::get('mysql_database'));
        $this->assertDatabaseExists($this->test_db_name);
        $this->assertUserExists(NEECHY_USER);
        $this->assertPageExists(NEECHY_USER);
        $this->assertUserExists($this->test_admin_name);
        $this->assertPageExists($this->test_admin_name);
    }

    public function testShouldInputInvalidDbCredentialsAndFail() {
        # Mock handler
        $mocked_methods = array('preamble',
                                'println',
                                'print_error',
                                'prompt_user');
        $handler = $this->mock_install_handler($mocked_methods);
        $handler->is_console = true;

        # Mock handler methods
        $handler->expects($this->any())->method('preamble');
        $handler->expects($this->any())->method('println');
        $handler->expects($this->any())->method('print_error')->will(
            $this->returnCallback(function($e) { throw $e; })
        );

        # Mock prompt user method.
        $mockedPromptUserValues = array(
            array('Enter database host', 'localhost', NeechyConfig::get('mysql_host')),
            array('Enter database user name', '', 'invalid'),
            array('Enter database user password', '', NeechyConfig::get('mysql_password')),
            array('Enter database name', 'neechy', $this->test_db_name),
        );
        $handler->expects($this->any())
                ->method('prompt_user')
                ->will($this->returnValueMap($mockedPromptUserValues));

        # Assert pre-exercise conditions
        $this->assertDatabaseDoesNotExist($this->test_db_name);

        # Exercise
        $this->setExpectedException('PDOException');
        $handler->handle();
    }

    public function testShouldInputInvalidEmailAddressAndFail() {
        # Mock handler
        $mocked_methods = array('preamble',
                                'println',
                                'read_page_body_from_template',
                                'print_error',
                                'prompt_user');
        $handler = $this->mock_install_handler($mocked_methods);
        $handler->is_console = true;

        # Mock handler methods
        $handler->expects($this->any())->method('preamble');
        $handler->expects($this->any())->method('println');
        $handler->expects($this->any())->method('read_page_body_from_template')
                ->will($this->returnValue('body cannot be null'));
        $handler->expects($this->any())->method('print_error')->will(
            $this->returnCallback(function($e) { throw $e; })
        );

        # Mock prompt user method.
        $mockedPromptUserValues = array(
            array('Enter database host', 'localhost', NeechyConfig::get('mysql_host')),
            array('Enter database user name', '', NeechyConfig::get('mysql_user')),
            array('Enter database user password', '', NeechyConfig::get('mysql_password')),
            array('Enter database name', 'neechy', $this->test_db_name),
            array('Please enter your new user name', '', $this->test_admin_name),
            array('Please enter your email', '', 'invalid'),
        );
        $handler->expects($this->any())
                ->method('prompt_user')
                ->will($this->returnValueMap($mockedPromptUserValues));

        # Assert pre-exercise conditions
        $this->assertDatabaseDoesNotExist($this->test_db_name);

        # Exercise
        $expected_message = 'Email cannot be validated. Install failed. Please start over.';
        $this->setExpectedException('NeechyInstallError', $expected_message);
        $handler->handle();

        # Verify makes it at least through database setup
        $this->assertEquals($this->test_db_name, NeechyConfig::get('mysql_database'));
        $this->assertDatabaseExists($this->test_db_name);
        $this->assertUserExists(NEECHY_USER);
    }

    public function testShouldThrowErrorWhenAppConfigFilePresent() {
        # Setup NeechyConfig with App Config present
        $test_db_name = NeechyConfig::get('mysql_database');
        $this->assertEquals('test', NeechyConfig::environment());

        # Generate app config file from test config
        $test_config_contents = file_get_contents(NeechyConfig::test_config_path());
        $app_config_contents = str_replace('neechy_test_config',
                                           'neechy_app_config',
                                           $test_config_contents);
        file_put_contents(NeechyConfig::app_config_path(), $app_config_contents);

        # Reload NeechyConfig with App Config present
        NeechyConfig::init();
        $this->assertEquals('app', NeechyConfig::environment());
        $this->assertEquals($test_db_name, NeechyConfig::get('mysql_database'));

        # Mock handler
        $handler = $this->mock_install_handler(array('print_error'));
        $handler->expects($this->any())->method('print_error')->will(
            $this->returnCallback(function($e) { throw $e; })
        );
        $handler->is_console = true;

        # Exercise
        $expected_message = 'App config file is already installed.';
        $this->setExpectedException('NeechyInstallError', $expected_message);
        $handler->handle();
    }

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
