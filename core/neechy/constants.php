<?php
/**
 * core/neechy/constants.php
 *
 * Neechy constants. Use sparingly.
 *
 */
require_once('../core/neechy/path.php');

#
# Version
#
define('NEECHY_VERSION', '0.1');
define('NEECHY_URL', 'https://github.com/klenwell/neechy');

#
# Path Constants
#
define('NEECHY_ROOT', dirname(dirname(dirname(__FILE__))));
define('NEECHY_APP_PATH', NeechyPath::join(NEECHY_ROOT, 'app'));
define('NEECHY_CONFIG_PATH', NeechyPath::join(NEECHY_ROOT, 'config'));
define('NEECHY_CORE_PATH', NeechyPath::join(NEECHY_ROOT, 'core'));
define('NEECHY_PUBLIC_PATH', NeechyPath::join(NEECHY_ROOT, 'public'));
define('NEECHY_CONSOLE_PATH', NeechyPath::join(NEECHY_ROOT, 'console'));
define('NEECHY_HANDLER_CORE_PATH', NeechyPath::join(NEECHY_CORE_PATH, 'handlers'));
define('NEECHY_HANDLER_APP_PATH', NeechyPath::join(NEECHY_APP_PATH, 'handlers'));
define('NEECHY_TASK_CONSOLE_PATH', NeechyPath::join(NEECHY_CONSOLE_PATH, 'tasks'));
define('NEECHY_TASK_APP_PATH', NeechyPath::join(NEECHY_APP_PATH, 'tasks'));

#
# MySQL / Database Constants
#
define('MYSQL_ENGINE', 'MyISAM');

#
# Regular Expression Patterns
#
define('RE_BRACKET_TOKENS', '/\{\{\s*[^\}]+\}\}/');
define('RE_EXTRACT_BRACKET_TOKEN_ID', '/[\{\}\s]/');

#
# Miscellaneous
#
define('NEECHY_USER', 'NeechySystem');
