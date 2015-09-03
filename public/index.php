<?php
/**
 * index.php
 *
 * This is the main Neechy script. This file is called each time a request is
 * made from the browser.
 *
 */
require_once('../core/services/web.php');

$config = NeechyConfig::init();
$web_service = new NeechyWebService($config);
$web_service->serve();
