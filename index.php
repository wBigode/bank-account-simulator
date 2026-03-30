<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$server = new Swoole\HTTP\Server("0.0.0.0", 9501);

$server->on("request", function ($request, $response) {
    $response->end("This is a test");
});

$server->start();

?>