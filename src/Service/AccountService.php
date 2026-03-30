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
}