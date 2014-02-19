<?php

//Server configuration
$_app_config = array(
    'servers' => array(
        'development' => array(
            'title' => 'Development server',
            'local_ip' => '192.168.20.170',
            //'external_ip' => '38.119.110.105',
        ),
        'staging' => array(
            'title' => 'Staging server',
            'local_ip' => '192.168.20.58',
        ),
        'production' => array(
            'title' => 'Production server',
            'local_ip' => '192.168.20.63',
    )));

define('CURRENT_SERVER_IP', $_app_config['servers']['production']['local_ip']);

if (isset($_app_config['servers'])) {
    foreach ($_app_config['servers'] as $key => $serverConfig) {
        if ($serverConfig['local_ip'] == CURRENT_SERVER_IP) {
            if (isset($_GET['callback'])) {
                header("Content-Type: application/x-javascript; charset=utf-8");
                echo $_GET['callback'] . '(' . json_encode($serverConfig) . ')';
            } else {
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode($serverConfig);
            }
            break;
        }
    }
}

exit;
