<?php
/**
 * index.php
 *
 * This is the main Neechy script. This file is called each time a request is
 * made from the browser.
 *
 */
require_once('../core/services/web.php');


class NeechyException extends Exception {}


$web_service = new NeechyWebService();

try {
    $response = $web_service->serve();
}
catch (NeechyException $e) {
    $response = $webservice->serve_error($e);
}

$response->send_headers();
$response->render();
