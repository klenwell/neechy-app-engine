<?php
/**
 * index.php
 *
 * This is the main Neechy script. This file is called each time a request is
 * made from the browser.
 *
 */
require_once('../app/services/app_engine.php');
require_once('../app/neechy/config.php');

$config = NeechyAppEngineConfig::init();
$web_service = new NeechyAppEngineService($config);
$web_service->serve();
