<?php

declare(strict_types=1);

namespace App\Http\Handler;

use App\Service\AccountService;

class ResetHandler
{
    public function __construct(
        private AccountService $accountService
    ) {
    }

    public function handle(): array
    {
        $this->accountService->reset();

        return [200, "OK"];
    }
}