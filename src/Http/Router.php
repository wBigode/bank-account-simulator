<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\AppException;
use App\Http\Handler\ResetHandler;
use Throwable;

class Router
{
    public function __construct(
        private ResetHandler $resetHandler
    ) {
    }

    public function dispatch($request, $response): void
    {
        try {
            [$status, $body] = $this->resetHandler->handle();

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