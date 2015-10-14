<?php
/**
 * core/neechy/config.php
 *
 * Neechy config module.
 *
 * ENVIRONMENT
 *
 * There are three possible config files:
 *
 * 1. Core config at CORE_PATH: this is packaged with repository and should always be present.
 * 2. Test config at TEST_PATH: this must be manually generated by developer after cloning repo.
 * 3. App config at APP_PATH: this is generated by the console InstallHandler script.
 *
 * There is also the STUB_PATH which is used by the install script to generate the app config file.
 *
 * STAGE
 *
 * For the App-Engine version, there are basically two stages: dev and cloud.
 *
 * Dev is the local dev server. Cloud is the producton appspot server.
 *
 */
require_once('../core/neechy/constants.php');
require_once('../core/neechy/path.php');
require_once('../core/neechy/errors.php');


class NeechyConfigError extends NeechyError {}


class NeechyConfig {
    #
    # Constants
    #
    const CORE_PATH = 'config/core.conf.php';
    const STUB_PATH = 'core/handlers/install/php/stub.config.php';
    const TEST_PATH = 'config/test.conf.php';
    const APP_PATH = 'config/app.conf.php';

    #
    # Properties
    #
    static private $instance = null;

    public $path = '';
    private $settings = array();
    private $environment = null;    # app, test, or core
    private $stage = null;          # dev or cloud
    private $core_loaded = false;
    private $test_loaded = false;
    private $app_loaded = false;

    #
    # Constructors
    #
    static public function init() {
        self::$instance = new NeechyConfig();
        return self::$instance;
    }

    public function __construct() {
        $this->environment = $this->compute_environment();
        $this->stage = $this->compute_stage();
        $this->settings = $this->load_settings();
    }

    #
    # Static Public Interface
    #
    static public function get($setting, $default=null) {
        if ( isset(self::$instance->settings[$setting]) ) {
            return self::$instance->settings[$setting];
        }
        else {
            return $default;
        }
    }

    static public function environment() {
        return self::$instance->environment;
    }

    static public function stage() {
        return self::$instance->stage;
    }

    static public function app_config_path() {
        return NeechyPath::join(NEECHY_ROOT, self::APP_PATH);
    }

    static public function test_config_path() {
        return NeechyPath::join(NEECHY_ROOT, self::TEST_PATH);
    }

    #
    # Protected Static Methods
    #
    static private function install_app_config_file($sleep=2) {
        $app_config_dir = dirname(self::app_config_path());
        if ( ! file_exists($app_config_dir) ) {
            mkdir($app_config_dir);
        }

        $app_config_path = self::app_config_path();
        $warning = sprintf("\n[WARNING] Creating user config file: %s\n\n", $app_config_path);
        echo $warning;

        $stub_config_path = NeechyPath::join(NEECHY_ROOT, self::STUB_PATH);
        copy($stub_config_path, $app_config_path);
    }

    #
    # Public Instance Methods
    #
    public function load_settings() {
        $core_settings = $this->load_core_config_file();

        if ( $this->environment == 'app' ) {
            $env_settings = $this->load_app_config_file();
        }
        elseif ( $this->environment == 'test' ) {
            $env_settings = $this->load_test_config_file();
        }

        $this->settings = array_merge($core_settings, $env_settings);
        return $this->settings;
    }

    public function update_setting($setting, $value) {
        $this->settings[$setting] = $value;
    }

    public function save() {
        $format = <<<HEREPHP
<?php
/**
 * Neechy App Configuration File
 *
 * This file was generated by the InstallHandler installer on %s
 *
 */

%s = array(
    %s
);
HEREPHP;

        $config_lines = array();
        foreach ( $this->_settings as $setting => $value ) {
            $config_lines[] = sprintf("'%s' => '%s',",
                str_replace("'", "/'", $setting),
                str_replace("'", "/'", $value)
            );
        }

        sort($config_lines);

        $content = sprintf($format,
            date('r'),
            '$neechy_app_config',
            implode("\n    ", $config_lines)
        );

        # Write file
        $file = @fopen($this->path, "w");
        fwrite($file, $content);
        fclose($file);
    }

    public function reload() {
        NeechyDatabase::disconnect_from_db();
        NeechyConfig::init();
    }

    #
    # Private Instance Methods
    #
    private function compute_environment() {
        # If app file present, load file and assume app environment.
        if ( $this->app_config_file_present() ) {
            return 'app';
        }

        # If app file missing, load test file and assume test environment.
        if ( $this->test_config_file_present() ) {
            return 'test';
        }

        # Default: core
        return 'core';
    }

    private function compute_stage() {
        $on_appspot = isset($_SERVER['SERVER_SOFTWARE']) &&
            strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false;

        if ( $on_appspot ) {
            return 'cloud';
        }
        else {
            return 'dev';
        }
    }

    private function app_config_file_present() {
        $app_config_path = NeechyPath::join(NEECHY_ROOT, self::APP_PATH);
        return file_exists($app_config_path);
    }

    private function test_config_file_present() {
        $test_config_path = NeechyPath::join(NEECHY_ROOT, self::TEST_PATH);
        return file_exists($test_config_path);
    }

    private function load_core_config_file() {
        $core_config_path = $this->assert_core_config_present();
        require($core_config_path);

        # Global variable assigned in core config file.
        $neechy_core_config['core-loaded'] = microtime();
        return $neechy_core_config;
    }

    private function load_app_config_file() {
        $app_config_path = NeechyPath::join(NEECHY_ROOT, self::APP_PATH);
        require($app_config_path);

        # Load default and stage settings
        $default_settings = $neechy_app_config['default'];
        $stage_settings = $neechy_app_config[$this->stage];
        $env_settings = array_merge($default_settings, $stage_settings);

        # Global variable assigned in app config file.
        $env_settings['app-loaded'] = microtime();
        return $env_settings;
    }

    private function load_test_config_file() {
        $test_config_path = NeechyPath::join(NEECHY_ROOT, self::TEST_PATH);
        require($test_config_path);

        # Global variable assigned in test config file.
        $neechy_test_config['test-loaded'] = microtime();
        return $neechy_test_config;
    }

    private function assert_core_config_present() {
        $core_config_path = NeechyPath::join(NEECHY_ROOT, self::CORE_PATH);
        if ( ! file_exists($core_config_path) ) {
            throw new NeechyConfigError(sprintf('Core config file [%s] missing',
                                                $core_config_path));
        }
        return $core_config_path;
    }
}
