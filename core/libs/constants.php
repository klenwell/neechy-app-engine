<?php
/**
 * core/libs/constants.php
 *
 * Neechy constants. Use sparingly.
 *
 */
require_once('../core/libs/utilities.php');

#
# Path Constants
#
define('NEECHY_ROOT', dirname(dirname(dirname(__FILE__))));
define('NEECHY_APP_PATH', NeechyPath::join(NEECHY_ROOT, 'app'));
define('NEECHY_CONFIG_PATH', NeechyPath::join(NEECHY_ROOT, 'config'));
define('NEECHY_CORE_PATH', NeechyPath::join(NEECHY_ROOT, 'core'));
define('NEECHY_PUBLIC_PATH', NeechyPath::join(NEECHY_ROOT, 'public'));

#
# MySQL / Database Constants
#
define('MYSQL_ENGINE', 'MyISAM');

#
# Regular Expression Patterns
#
define('RE_BRACKET_TOKENS', '/\{\{\s*[^\}]+\}\}/');
define('RE_EXTRACT_BRACKET_TOKEN_ID', '/[\{\}\s]/');
