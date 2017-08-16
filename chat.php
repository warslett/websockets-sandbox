<?php

require_once "vendor/autoload.php";

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use BiffBangPow\WebSockets\Chat;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(new WsServer(new Chat())),
    8910
);

$server->run();
