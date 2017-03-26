<?php
#
# Phpunit Bootstrap File
#
# Want to see STRICT errors
error_reporting(E_ALL);

# App expects to execute from subdirectory (pubic for web, test for tests)
$test_dir = dirname(__FILE__);
chdir($test_dir);

# Need to load Google SDK
$app_engine_sdk_path = '../../../../google-cloud-sdk/platform/google_appengine/php/sdk';
set_include_path(get_include_path() . PATH_SEPARATOR . $app_engine_sdk_path);

require_once 'google/appengine/runtime/autoloader.php';
require_once 'google/appengine/runtime/ApiProxyTest.php';
use google\appengine\runtime\ApiProxyTest\make_call;

# Set NEECHY_ENV var for config class
$_ENV['NEECHY_ENV'] = 'test';

# Load config class
require_once('../core/neechy/config.php');

# Require test config file
if ( ! file_exists(NeechyConfig::test_config_path()) ) {
    throw new Exception(sprintf('Test config file [%s] missing.',
                                NeechyConfig::test_config_path()));
}
