<?php
/**
 * app/neechy/config.php
 *
 * Neechy App Engine config module. Override core config file
 *
 * STAGE
 *
 * For the App-Engine version, there are basically two stages: dev and cloud.
 *
 * Dev is the local dev server. Cloud is the producton appspot server.
 *
 */
require_once('../core/neechy/config.php');


class NeechyAppEngineConfig extends NeechyConfig {
    #
    # Constants
    #

    #
    # Properties
    #
    private $stage = null;          # dev or cloud

    #
    # Constructors
    #
    public function __construct() {
        $this->environment = $this->compute_environment();
        $this->stage = $this->compute_stage();
        $this->settings = $this->load_settings();
    }

    #
    # Static Public Interface
    #
    static public function stage() {
        return self::$instance->stage;
    }

    #
    # Protected Static Methods
    #

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

    #
    # Private Instance Methods
    #
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
}
