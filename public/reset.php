<?php

require_once('../core/neechy/config.php');

# Parse params
$params = explode('/', $_SERVER["REQUEST_URI"]);
$action = count($params) > 2 ? $params[2] : null;
var_dump($params);

# Config Settings
$config = NeechyConfig::init();

# Router
if ( $action == 'database' ) {
    var_dump($config);
}
else {
    echo 'Action Not Set';
}
