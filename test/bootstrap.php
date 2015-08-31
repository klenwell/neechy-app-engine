<?php
#
# Phpunit Bootstrap File
#
# App expects to execute from subdirectory (pubic for web, test for tests)
$test_dir = dirname(__FILE__);
chdir($test_dir);

# Verify test config file present
require_once('../core/neechy/config.php');

$test_config_file = NeechyConfig::test_config_path();
if ( ! file_exists($test_config_file) ) {
    throw new Exception(sprintf('Test config file [%s] missing.', $test_config_file));
}
