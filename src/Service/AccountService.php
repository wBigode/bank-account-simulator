<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\AccountRepositoryInterface;

class AccountService
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository
    ) {
    }

    public function reset(): void
    {
        $this->accountRepository->reset();
    }

    public function getBalance(string $accountId): ?int
    {
        $account = $this->accountRepository->find($accountId);

        return $account ? $account['balance'] : null;
    }

    public function deposit(string $accountId, int $amount): array
    {
        $account = $this->findOrCreateAccount($accountId);
        $account['balance'] += $amount;

        $this->accountRepository->save($accountId, $account);

        return $account;
    }

    public function withdraw(string $accountId, int $amount): ?array
    {
        $account = $this->accountRepository->find($accountId);

        if ($account === null || $account['balance'] < $amount) {
            return null;
        }

        $account['balance'] -= $amount;
        $this->accountRepository->save($accountId, $account);

        return $account;
    }

    public function transfer(string $originId, string $destinationId, int $amount): ?array
    {
        $origin = $this->accountRepository->find($originId);

        if ($origin === null || $origin['balance'] < $amount) {
            return null;
        }

        $destination = $this->findOrCreateAccount($destinationId);

        $origin['balance'] -= $amount;
        $destination['balance'] += $amount;

        $this->accountRepository->save($originId, $origin);
        $this->accountRepository->save($destinationId, $destination);

        return [
            'origin' => $origin,
            'destination' => $destination
        ];
    }

    private function findOrCreateAccount(string $accountId): array
    {
        $account = $this->accountRepository->find($accountId);

        if ($account === null) {
            $account = ['id' => $accountId, 'balance' => 0];
        }

        return $account;
    }
}