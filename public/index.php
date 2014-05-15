<?php
/**
 * index.php
 *
 * This is the main Neechy script. This file is called each time a request is
 * made from the browser.
 *
 */
require_once('../core/services/web.php');

$web_service = new NeechyWebService();
$web_service->serve();
