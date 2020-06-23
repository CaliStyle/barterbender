<?php
define('YNCHAT_SERVER', true);

//require(__DIR__ . '/lib/SplClassLoader.php');
require_once 'cli.php';
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\ynchat;

require 'vendor/autoload.php';
// require_once ('ynapp/ynchat.php');

$config = $oYNChat->getSocketConfig();
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Ynchat($oYNChat)
        )
    ),
    (int)$config['iPort']
);

$server->run();


