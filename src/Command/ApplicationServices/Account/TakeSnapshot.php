<?php
namespace App\Command\ApplicationServices\Account;

use App\Command\Infrastructure\Repository\AccountRepository;

class TakeSnapshot
{
    /** @var AccountRepository */
    private $repository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->repository = $accountRepository;
    }

    public function handle(TakeSnapshotRequest $command)
    {
        $account = $this->repository->findByEmail($command->getEmail());

        if (!$account) {
            return;
        }

        $this->repository->takeSnapshot($account);
    }
}
