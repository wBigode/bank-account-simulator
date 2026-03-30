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
}