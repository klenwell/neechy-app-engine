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
        try {
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

    #
    # Protected Methods (private cannot be mocked)
    #
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
        $this->validate_database_connection();
        $this->validate_database_safe_to_write();

        # Create database
        $this->create_database();
    }

    protected function save_and_reload_app_config_file() {
        # This will create file if it does not exist
        $app_config = NeechyConfig::load_app_config();

        # Replace database settings
        $app_config->update_setting('mysql_host', $this->db_host);
        $app_config->update_setting('mysql_user', $this->db_user);
        $app_config->update_setting('mysql_password', $this->db_pass);
        $app_config->update_setting('mysql_database', $this->db_name);
        $app_config->save();

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

        while (! $name_is_valid) {
            $validator = new SignUpValidator();
            $name = $this->prompt_user('Please enter your new user name');

            if ( ! $validator->validate_signup_user($name, 'name') ) {
                $m = sprintf('invalid user name: %s',
                    implode(', ', $validator->errors['name']));
                $this->println($m);
            }
            else {
                $name_is_valid = true;
            }
        }

        while (! $email_is_valid) {
            $validator = new SignUpValidator();
            $email = $this->prompt_user('Please enter your email');

            if ( ! $validator->validate_signup_email($email, 'email') ) {
                $m = sprintf('invalid email address: %s',
                    implode(', ', $validator->errors['email']));
                $this->println($m);
            }
            else {
                $email_is_valid = true;
            }
        }

        $password = NeechySecurity::random_hex();
        $level = 'ADMIN';

        # Create user and default page
        $user = User::register($name, $email, $password, $level);

        # Create default page
        $path = NeechyPath::join($this->html_path(), 'owner-page.md.php');
        $page = Page::find_by_title($user->field('name'));
        $page->set('body', $this->read_page_body_from_template($path));
        $page->set('editor', 'NeechySystem');
        $page->save();

        # Feedback
        $format = <<<STDOUT
An admin has been created with your user name: %s
Your random password is: %s

Please login now and change your password.
STDOUT;

        $this->println(sprintf($format, $name, $password));
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
    private function connect_to_database_host() {
        $host = sprintf('mysql:host=%s', $this->db_host);
        $pdo = new PDO($host, $this->db_user, $this->db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    private function database_exists() {
        $sql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?';

        $pdo = $this->connect_to_database_host();
        $query = $pdo->prepare($sql);
        $query->execute(array($this->db_name));
        $row = $query->fetch(PDO::FETCH_ASSOC);

        return ((bool) $row);
    }

    private function create_database() {
        $this->println('Creating database');
        $this->drop_database_if_exists();
        $pdo = $this->connect_to_database_host();
        $pdo->exec(sprintf('CREATE DATABASE `%s`', $this->db_name));
        return $pdo;
    }

    private function drop_database_if_exists() {
        $database = NeechyConfig::get('mysql_database');
        $pdo = $this->connect_to_database_host();
        $pdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $this->db_name));
        return $pdo;
    }

    private function create_model_tables() {
        $this->println('Creating database tables');
        $models = array('Page', 'User');

        foreach ( $models as $model_name ) {
            $model = new $model_name();
            $model_class = get_class($model);

            if ( $model_class::table_exists() ) {
                $this->println(sprintf("%s table exists", $model_name));
            }
            else {
                $model_class::create_table_if_not_exists();
                $this->println(sprintf("created %s table", $model_name));
            }
        }
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

    private function validate_database_connection() {
        return $this->connect_to_database_host();
    }

    private function validate_database_safe_to_write() {
        $db_exists = $this->database_exists();

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
