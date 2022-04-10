<?php
$server = new Swoole\Http\Server("127.0.0.1", 8080);

$server->on("request", function (Swoole\Http\Request $rq, Swoole\Http\Response $rs) {
    $rs->header("Content-type", "application/json");
    $rs->end('{"status":"OK"}');
});

$server->start();

