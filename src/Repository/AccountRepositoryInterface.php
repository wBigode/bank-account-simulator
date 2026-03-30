<?php

declare(strict_types=1);

namespace App\Repository;

interface AccountRepositoryInterface
{
    public function reset(): void;
    public function find(string $id): ?array;
}