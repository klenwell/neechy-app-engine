<?php
#
# Phpunit Bootstrap File
#
# App expects to execute from subdirectory (pubic for web, test for tests)
$test_dir = dirname(__FILE__);
chdir($test_dir);

# Load config class
require_once('../core/neechy/config.php');

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
