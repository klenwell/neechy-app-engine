<?php

require_once('../core/neechy/config.php');
require_once('../core/neechy/database.php');
require_once('../core/neechy/response.php');

# Parse params
$params = explode('/', $_SERVER["REQUEST_URI"]);
$action = count($params) > 2 ? $params[2] : null;

# Must init config for NeechyDatabase
NeechyConfig::init();

# Response
$json = array('params' => $params, 'action' => $action);

# Router
if ( $action == 'database' ) {
    NeechyDatabase::reset();
    $json['result'] = 'ok';
    syslog(LOG_INFO, 'Database reset.');
}
else {
    $json['warning'] = 'Invalid action.';
}

$response = new NeechyResponse(json_encode($json), 200);
$response->send_headers();
$response->render();
