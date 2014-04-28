<?php
/**
 * core/libs/constants.php
 *
 * Neechy constants. Use sparingly.
 *
 */
require_once('../core/libs/utilities.php');


define('NEECHY_ROOT', dirname(dirname(dirname(__FILE__))));
define('NEECHY_APP_PATH', NeechyPath::join(NEECHY_ROOT, 'app'));
define('NEECHY_CONFIG_PATH', NeechyPath::join(NEECHY_ROOT, 'config'));
define('NEECHY_CORE_PATH', NeechyPath::join(NEECHY_ROOT, 'core'));
define('NEECHY_PUBLIC_PATH', NeechyPath::join(NEECHY_ROOT, 'public'));
