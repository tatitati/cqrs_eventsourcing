<?php
namespace App\Command\Infrastructure\Repository;

use App\Command\Domain\Account\Account;

interface iAccountRepository
{
    public function save(Account $account): void;
    public function findByEmail(string $email): ?Account;
}
