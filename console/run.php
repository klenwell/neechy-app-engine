<?php
/**
 * run.php
 *
 * This is the main Neechy console script. It provides a command line interface
 * for running handlers and tasks.
 *
 * Need to chdir as first step since, by convention, this script should be
 * run from the neechy root folder, yet Neechy assumes it will be run from a
 * subdirectory like public.
 *
 */
chdir(__DIR__);
require_once('../core/services/console.php');

$config = NeechyConfig::init();
$console_service = new NeechyConsoleService($config);
$console_service->serve();
