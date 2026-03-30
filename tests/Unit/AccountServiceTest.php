<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\AccountRepository;
use App\Service\AccountService;
use PHPUnit\Framework\TestCase;

class AccountServiceTest extends TestCase
{
    private AccountService $accountService;

    protected function setUp(): void
    {
        $repository = new AccountRepository();
        $this->accountService = new AccountService($repository);
    }

    /**
     * RESET TESTS
     */

    public function testResetAccounts(): void
    {
        $this->accountService->deposit('100', 10);
        $this->accountService->deposit('200', 20);

        $this->accountService->reset();

        $this->assertNull($this->accountService->getBalance('100'));
        $this->assertNull($this->accountService->getBalance('200'));
    }

    /**
     * BALANCE TESTS
     */

    public function testGetBalanceReturnsValue(): void
    {
        $this->accountService->deposit('100', 10);

        $this->assertSame(10, $this->accountService->getBalance('100'));
    }

    public function testGetBalanceNotExistingAccount(): void
    {
        $this->assertNull($this->accountService->getBalance('999'));
    }

    public function testGetBalanceNotModifyState(): void
    {
        $this->accountService->deposit('100', 10);

        $this->accountService->getBalance('100');
        $this->accountService->getBalance('100');

        $this->assertSame(10, $this->accountService->getBalance('100'));
    }

    /**
     * DEPOSIT TESTS
     */

    public function testDepositCreateAccountIfNotExists(): void
    {
        $result = $this->accountService->deposit('100', 10);

        $this->assertSame("100", $result["id"]);
        $this->assertSame(10, $result["balance"]);
    }


    public function testDepositUpdateAccount(): void
    {
        $this->accountService->deposit('100', 10);

        $result = $this->accountService->deposit('100', 10);

        $this->assertSame('100', $result["id"]);
        $this->assertSame(20, $result["balance"]);
    }

    public function testDepositReturnsCorrectTypes(): void
    {
        $result = $this->accountService->deposit('100', 10);

        $this->assertIsString($result["id"]);
        $this->assertIsInt($result["balance"]);
    }

    /**
     * WITHDRAW TESTS
     */

    public function testWithdrawReducesBalance(): void
    {
        $this->accountService->deposit('100', 20);

        $result = $this->accountService->withdraw('100', 5);

        $this->assertSame('100', $result["id"]);
        $this->assertSame(15, $result["balance"]);
    }

    public function testWithdrawReturnsNullOnNonExistingAccount(): void
    {
        $result = $this->accountService->withdraw('999', 10);

        $this->assertNull($result);
    }

    public function testFullWithdrawReturnsZero(): void
    {
        $this->accountService->deposit('100', 20);

        $result = $this->accountService->withdraw('100', 20);

        $this->assertSame('100', $result["id"]);
        $this->assertSame(0, $result["balance"]);
    }

    public function testWithdrawWithAmountGreaterThanBalanceReturnsNull(): void
    {
        $this->accountService->deposit('100', 10);

        $result = $this->accountService->withdraw('100', 15);

        $this->assertNull($result);
    }

    public function testWithdrawWithAmountGreaterThanBalanceDoesNotModifyAccount(): void
    {
        $this->accountService->deposit('100', 10);

        $this->accountService->withdraw('100', 15);

        $this->assertSame(10, $this->accountService->getBalance('100'));
    }

    /**
     * TRANSFER TESTS
     */

    public function testTransferMovesAmount(): void
    {
        $this->accountService->deposit('100', 20);

        $result = $this->accountService->transfer('100', '200', 15);

        $this->assertSame('100', $result["origin"]["id"]);
        $this->assertSame(5, $result["origin"]["balance"]);

        $this->assertSame('200', $result["destination"]["id"]);
        $this->assertSame(15, $result["destination"]["balance"]);
    }

    public function testTransferCreatesDestinationAccount(): void
    {
        $this->accountService->deposit('100', 20);

        $result = $this->accountService->transfer('100', '200', 10);

        $this->assertSame('200', $result["destination"]["id"]);
        $this->assertSame(10, $result["destination"]["balance"]);
    }

    public function testTransferPreservesTotalAmount(): void
    {
        $this->accountService->deposit('100', 20);
        $this->accountService->deposit('200', 10);
        $this->accountService->transfer('100', '200', 10);

        $total = $this->accountService->getBalance('100') + $this->accountService->getBalance('200');

        $this->assertSame(30, $total);
    }

    public function testTransferToNonExistingOriginAccountReturnsNull(): void
    {
        $result = $this->accountService->transfer('999', '200', 10);

        $this->assertNull($result);
    }

    public function testTransferFailureDoesNotModifyDestinationAccount(): void
    {
        $this->accountService->deposit('100', 10);

        $this->accountService->transfer('200', '100', 10);

        $this->assertSame(10, $this->accountService->getBalance('100'));
    }

    public function testTransferWithInsufficientAmountDoesNotModifyAccounts(): void
    {
        $this->accountService->deposit('100', 10);
        $this->accountService->deposit('200', 10);

        $this->accountService->transfer('100', '200', 15);

        $this->assertSame(10, $this->accountService->getBalance('100'));
        $this->assertSame(10, $this->accountService->getBalance('200'));
    }
}