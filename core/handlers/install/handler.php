<?php
/**
 * core/handlers/install/handler.php
 *
 * Handles install process.
 *
 * Console (CLI):
 *  php console/run.php install
 *
 */
require_once('../core/handlers/base.php');
require_once('../core/neechy/config.php');
require_once('../core/neechy/constants.php');
require_once('../core/neechy/path.php');
require_once('../core/handlers/auth/php/validator.php');
require_once('../core/models/page.php');
require_once('../core/models/user.php');


class NeechyInstallError extends NeechyError {}


class InstallHandler extends NeechyHandler {

    #
    # Properties
    #
    # Default pages
    public static $default_pages = array(
        'home',
        'NeechyFormatting',
        NEECHY_USER
    );

    public $service = null;
    public $html_report = array();
    public $is_console = false;
    private $reset_database = false;

    private $db_host = 'localhost';
    private $db_name = 'neechy';
    private $db_user = '';
    private $db_pass = '';

    #
    # Public Methods
    #
    public function handle() {
        if ( $this->is_console ) {
            $this->handle_in_console();
        }
        else {
            return '<h4>Install runs from the console.</h4>';
        }
    }

    public function setup_dev() {
        # Specifically for running development web server against source.
        $this->create_database(NeechyConfig::get('mysql_host'),
                               NeechyConfig::get('mysql_user'),
                               NeechyConfig::get('mysql_password'),
                               NeechyConfig::get('mysql_database'));
        $this->create_model_tables();
        $this->create_neechy_user();
        $this->create_default_pages();

        # Create admin user
        $name = 'NeechyAdmin';
        $email = 'neechyadmin@neechy.org';
        $password = 'neechy123';
        $this->register_admin_user($name, $email, $password);
    }

    #
    # Protected Methods (private cannot be mocked)
    #
    protected function handle_in_console() {
        try {
            $this->assert_app_config_not_present();
            $this->preamble();
            $this->setup_database();
            $this->save_and_reload_app_config_file();
            $this->create_model_tables();
            $this->create_neechy_user();
            $this->create_default_pages();
            $this->create_admin_user();
        }
        catch (Exception $e) {
            $this->print_error($e);
        }
    }

    protected function assert_app_config_not_present() {
        if ( NeechyConfig::environment() == 'app' ) {
            throw new NeechyInstallError('App config file is already installed.');
        }
    }

    protected function preamble() {
        $preamble = <<<PREAMBLE

** NEECHY INSTALLATION SCRIPT **

This script will install Neechy on your system for you.

Before you begin, you should create a MySQL database user with CREATE privileges
and have that user's name and password ready.

If your MySQL user is not yet ready, please ready the user now.

PREAMBLE;

        $this->println($preamble);
        $this->prompt_user("When ready, hit 'ENTER' to continue");
    }

    protected function setup_database() {
        $this->print_header('Setting Up Database');

        # Prompt user for database settings
        $this->db_host = $this->prompt_user("Enter database host", $this->db_host);
        $this->db_user = $this->prompt_user("Enter database user name", $this->db_user);
        $this->db_pass = $this->prompt_user("Enter database user password", $this->db_pass);
        $this->db_name = $this->prompt_user("Enter database name", $this->db_name);

        # Validations
        $this->validate_database_connection($this->db_host, $this->db_user, $this->db_pass,
                                            $this->db_name);
        $this->validate_database_safe_to_write($this->db_host, $this->db_user,
                                               $this->db_pass, $this->db_name);

        # Create database
        $this->create_database($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
    }

    protected function save_and_reload_app_config_file() {
        $app_config = NeechyConfig::init();

        # Replace database settings
        $app_config->update_setting('mysql_host', $this->db_host);
        $app_config->update_setting('mysql_user', $this->db_user);
        $app_config->update_setting('mysql_password', $this->db_pass);
        $app_config->update_setting('mysql_database', $this->db_name);
        $app_config->save_app_config_file();

        # Reload config settings
        $app_config->reload();
    }

    protected function create_neechy_user() {
        $this->print_header('Create Default Users');

        $name = NEECHY_USER;
        $email = 'no-reply@neechy.github.com';
        $password = NeechySecurity::random_hex();

        $user = User::register($name, $email, $password);
        $this->println('NeechySystem user created');
    }

    protected function create_default_pages() {
        $this->print_header('Create Default Pages');
        $pages_created = array();
        $glob_target = NeechyPath::join($this->html_path(), '*.md.php');

        foreach(self::$default_pages as $name) {
            $basename = sprintf('%s.md.php', $name);
            $path = NeechyPath::join($this->html_path(), $basename);
            $page = Page::find_by_title($name);
            $page->set('body', $this->read_page_body_from_template($path));
            $page->set('editor', NEECHY_USER);
            $page->save();
            $pages_created[] = $page;
            $this->println(sprintf('Created page: %s', $name));
        }

        $this->println(sprintf('Created %d pages', count($pages_created)));
    }

    protected function create_admin_user() {
        $this->print_header('Create Admin User');

        $name_is_valid = false;
        $email_is_valid = false;

        # Choose name (5 tries)
        $strikes = 5;
        while (! $name_is_valid) {
            $validator = new SignUpValidator();
            $name = $this->prompt_user('Please enter your new user name');

            if ( ! $validator->validate_signup_user($name, 'name') ) {
                $m = sprintf('invalid user name: %s',
                    implode(', ', $validator->errors['name']));
                $this->println($m);
                $strikes--;
            }
            else {
                $name_is_valid = true;
            }

            if ( $strikes < 1 ) {
                $m = 'User name cannot be validated. Install failed. Please start over.';
                throw new NeechyInstallError($m);
            }
        }

        # Input email
        $strikes = 5;
        while (! $email_is_valid) {
            $validator = new SignUpValidator();
            $email = $this->prompt_user('Please enter your email');

            if ( ! $validator->validate_signup_email($email, 'email') ) {
                $m = sprintf('invalid email address: %s',
                    implode(', ', $validator->errors['email']));
                $this->println($m);
                $strikes--;
            }
            else {
                $email_is_valid = true;
            }

            if ( $strikes < 1 ) {
                $m = 'Email cannot be validated. Install failed. Please start over.';
                throw new NeechyInstallError($m);
            }
        }

        # Register user and create page
        $password = NeechySecurity::random_hex();
        $this->register_admin_user($name, $email, $password);

        # Feedback
        $format = <<<STDOUT
An admin has been created with your user name: %s
Your random password is: %s

Please login now and change your password.
STDOUT;

        $this->println(sprintf($format, $name, $password));
    }

    protected function register_admin_user($name, $email, $password) {
        $level = 'ADMIN';

        # Create user and default page
        $user = User::register($name, $email, $password, $level);

        # Create default page
        $path = NeechyPath::join($this->html_path(), 'owner-page.md.php');
        $page = Page::find_by_title($user->field('name'));
        $page->set('body', $this->read_page_body_from_template($path));
        $page->set('editor', 'NeechySystem');
        $page->save();

        return $page;
    }

    protected function print_error($e) {
        $message = ($e instanceof Exception) ? $e->getMessage() : (string) $e;

        $format = <<<STDOUT

The following error occurred:

%s

Please double check your input and try again.

STDOUT;

        if ( $this->is_console ) {
            $this->println(sprintf($format, $message));
            die(1);
        }
        else {
            throw $e;
        }
    }

    protected function command_line_param($n) {
        if ( ! isset($this->service->params[$n]) ) {
            return null;
        }
        else {
            return $this->service->params[$n];
        }
    }

    protected function read_page_body_from_template($path) {
        # This method makes it easier to mock out during testing.
        return $this->t->render_partial_by_path($path);
    }

    #
    # Database Methods
    #
    private function connect_to_database_host($host, $user, $pass) {
        $dsn = sprintf('mysql:host=%s', $host);
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    private function database_exists($host, $user, $pass, $db_name) {
        $sql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?';

        $pdo = $this->connect_to_database_host($host, $user, $pass);
        $query = $pdo->prepare($sql);
        $query->execute(array($db_name));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        return ((bool) $row);
    }

    private function create_database($host, $user, $pass, $db_name) {
        $this->println('Creating database');
        $this->drop_database_if_exists($host, $user, $pass, $db_name);
        $pdo = $this->connect_to_database_host($host, $user, $pass);
        $pdo->exec(sprintf('CREATE DATABASE `%s`', $db_name));
        return $pdo;
    }

    private function drop_database_if_exists($host, $user, $pass, $db_name) {
        $database = NeechyConfig::get('mysql_database');
        $pdo = $this->connect_to_database_host($host, $user, $pass);
        $pdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $db_name));
        return $pdo;
    }

    private function create_model_tables() {
        $this->println('Creating database tables');
        $tables_created = NeechyDatabase::create_model_tables();
        $this->println(sprintf("Created tables: %s", join(', ', $tables_created)));
    }

    #
    # Validators
    #
    private function validate_config_settings() {
        $db_name = NeechyConfig::get('mysql_database');

        if ( (! $db_name) || ($db_name == 'NULL') ) {
            throw new NeechyError(sprintf(
                'please update database settings in your config file: %s',
                NeechyConfig::path()
            ));
        }
        else {
            return true;
        }
    }

    private function validate_database_connection($host, $user, $pass, $db_name) {
        return $this->connect_to_database_host($host, $user, $pass, $db_name);
    }

    private function validate_database_safe_to_write($host, $user, $pass, $db_name) {
        $db_exists = $this->database_exists($host, $user, $pass, $db_name);

        # Database does not yet exist: safe
        if ( ! $db_exists ) {
            return true;
        }

        # Database exists, no force param: unsafe
        elseif ( $this->command_line_param(0) != 'force' ) {
            $warning = <<<STDOUT
Aborting install: database already exists.

To force the installer to overwrite the existing database, include the 'force'
parameter:

    php console/run.php install force
STDOUT;
            throw new NeechyError($warning);
        }

        # Database exists, force param, prompt
        else {
            # No heredoc: want extra space at end (gets removed by my editor if I
            # use a heredoc)
            $prompt_format = "
WARNING:
This script will overwrite your existing database: %s

Enter 'Y' to continue, any other key to abort: ";

            $response = $this->prompt_user(sprintf($prompt_format, $this->db_name));

            # Prompt yes: safe
            $response = substr($response, 0, 1);

            if ( $response == 'Y' ) {
                return true;
            }

            # Prompt no: unsafe
            else {
                throw new NeechyError('Install aborted; database preserved');
            }
        }
    }
}
