<?php

declare(strict_types=1);

namespace App\Http\Handler;

use App\Service\AccountService;

class BalanceHandler
{
    public function __construct(
        private AccountService $accountService
    ) {
    }

    public function handle($request)
    {
        parse_str($request->server['query_string'] ?? '', $query);
        $accountId = $query['account_id'] ?? '';

        $balance = $this->accountService->getBalance($accountId);

        if ($balance === null) {
            return [404, "0"];
        }

        return [200, (string) $balance];
    }
}