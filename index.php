<?php

declare(strict_types=1);

use App\Http\Handler\BalanceHandler;
use App\Http\Handler\ResetHandler;
use App\Http\Router;
use App\Repository\AccountRepository;
use App\Service\AccountService;

require __DIR__ . '/vendor/autoload.php';

$repository = new AccountRepository();
$service = new AccountService($repository);

$router = new Router(
    new ResetHandler($service),
    new BalanceHandler($service)
);

$server = new Swoole\HTTP\Server("0.0.0.0", 9501);

$server->on("request", function ($request, $response) use ($router) {
    $router->dispatch($request, $response);
});

$server->start();

?>