<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\AppException;
use App\Http\Handler\BalanceHandler;
use App\Http\Handler\ResetHandler;
use Throwable;

class Router
{
    public function __construct(
        private ResetHandler $resetHandler,
        private BalanceHandler $balanceHandler
    ) {
    }

    public function dispatch($request, $response): void
    {
        try {
            $method = $request->server["request_method"] ?? '';
            $path = parse_url($request->server["request_uri"] ?? '', PHP_URL_PATH);

            [$status, $body] = match(true) {
                $method === 'POST' && $path === '/reset' => $this->resetHandler->handle(),
                $method === 'GET' && $path === '/balance' => $this->balanceHandler->handle($request),
                default => [404, "0"],
            };

        } catch (AppException $e) {
            $status = $e->getStatusCode();
            $body = $response->getMessage();
        } catch (Throwable $e) {
            $status = 500;
            $body = "Internal Server Error";
        }

        $response->status($status);
        $response->end($body);
    }
}