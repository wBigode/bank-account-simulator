<?php

declare(strict_types=1);

namespace App\Http\Handler;

use App\Exception\AppException;
use App\Service\AccountService;

class EventHandler
{
    public function __construct(
        private AccountService $accountService
    ) {
    }

    public function handle($request): array
    {
        $body = json_decode($request->rawContent(), true);

        if (!is_array($body) || !isset($body["type"])) {
            throw new AppException("Invalid JSON!");
        }

        return match ($body["type"]) {
            "deposit" => $this->handleDeposit($body),
            "withdraw" => $this->handleWithdraw($body),
            "transfer" => $this->handleTransfer($body),
            default => [400, ""],
        };
    }

    private function handleDeposit(array $body): array
    {
        if (!isset($body["destination"], $body["amount"])) {
            return [400, ""];
        }

        $this->validateAccountId((string) $body["destination"]);
        $this->validateAmount($body["amount"]);

        $result = $this->accountService->deposit(
            (string) $body["destination"],
            (int) $body["amount"]
        );

        return [201, json_encode(["destination" => $result])];
    }

    private function handleWithdraw(array $body): array
    {
        if (!isset($body["origin"], $body["amount"])) {
            return [400, ""];
        }

        $this->validateAccountId((string) $body["origin"]);
        $this->validateAmount($body["amount"]);

        $result = $this->accountService->withdraw(
            (string) $body["origin"],
            (int) $body["amount"]
        );

        if ($result === null) {
            return [404, "0"];
        }

        return [201, json_encode(["origin" => $result])];
    }

    private function handleTransfer(array $body): array
    {
        if (!isset($body["origin"], $body["amount"], $body["destination"])) {
            return [400, ""];
        }

        $this->validateAccountId((string) $body["origin"]);
        $this->validateAccountId((string) $body["destination"]);
        $this->validateAmount($body["amount"]);

        $result = $this->accountService->transfer(
            (string) $body["origin"],
            (string) $body["destination"],
            (int) $body["amount"]
        );

        if ($result === null) {
            return [404, "0"];
        }

        return [201, json_encode($result)];
    }

    private function validateAmount(mixed $amount): void
    {
        if (!is_int($amount) || $amount <= 0) {
            throw new AppException("Invalid amount!");
        }
    }

    private function validateAccountId(string $accountId): void
    {
        if ($accountId === '' || !preg_match('/^[0-9]+$/', $accountId)) {
            throw new AppException("Invalid account ID!");
        }
    }
}