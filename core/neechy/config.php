<?php
/**
 * core/neechy/config.php
 *
 * Neechy config module.
 *
 */
require_once('../core/neechy/path.php');


class NeechyConfig {
    #
    # Constants
    #
    const CORE_PATH = 'core/config/core.conf.php';
    const USER_PATH = 'config/neechy.conf.php';

    #
    # Properties
    #
    static private $config = array();

    #
    # Public Static Methods
    #
    static public function init($path=NULL) {
        $core_settings = self::load_core_config_file();
        $user_settings = self::load_user_config_file($path);
        self::$config = array_merge($core_settings, $user_settings);
    }

    static public function get($setting, $default=NULL) {
        if ( isset(self::$config[$setting]) ) {
            return self::$config[$setting];
        }
        else {
            return $default;
        }
    }

    static public function path() {
        return NeechyPath::join(NEECHY_ROOT, self::USER_PATH);
    }

    #
    # Private Static Methods
    #
    static private function load_core_config_file($path=NULL) {
        $path = NeechyPath::join(NEECHY_ROOT, self::CORE_PATH);
        require($path);
        return $neechy_core_config;
    }

    static private function load_user_config_file($path=NULL) {
        $path = ( $path ) ? $path : NeechyPath::join(NEECHY_ROOT, self::USER_PATH);
        require($path);
        return $neechy_config;
    }
}
