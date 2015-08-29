<?php
#
# Phpunit Bootstrap File
#

$test_dir = dirname(__FILE__);
$test_config_file = 'test.conf.php';

chdir($test_dir);

if ( ! file_exists($test_config_file) ) {
    throw new Exception(sprintf('Test config file [%s] missing.', $test_config_file));
}
