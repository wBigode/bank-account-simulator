<?php

declare(strict_types=1);

namespace App\Repository;

class AccountRepository implements AccountRepositoryInterface
{
    private array $accounts = [];

    public function reset(): void
    {
        $this->accounts = [];
    }

    public function find(string $id): ?array
    {
        return $this->accounts[$id] ?? null;
    }

    public function save(string $id, array $account): void
    {
        $this->accounts[$id] = $account;
    }
}