<?php
#
# Phpunit Bootstrap File
#
# Want to see STRICT errors
error_reporting(E_ALL);

# App expects to execute from subdirectory (pubic for web, test for tests)
$test_dir = dirname(__FILE__);
chdir($test_dir);

# Load config class
require_once('../core/neechy/config.php');

# Set NEECHY_ENV var for config class
$_ENV['NEECHY_ENV'] = 'test';

# Halt if app config present (may destroy app data)
if ( file_exists(NeechyConfig::app_config_path()) ) {
    throw new Exception(sprintf('Abort: app config file [%s] present.',
                                NeechyConfig::app_config_path()));
}

# Require test config file
if ( ! file_exists(NeechyConfig::test_config_path()) ) {
    throw new Exception(sprintf('Test config file [%s] missing.',
                                NeechyConfig::test_config_path()));
}
