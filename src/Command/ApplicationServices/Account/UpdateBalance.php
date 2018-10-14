<?php
namespace App\Command\ApplicationServices\Account;

use App\Command\Infrastructure\Projections\Projector;
use App\Command\Infrastructure\Repository\AccountRepository;

class UpdateBalance
{
    /** @var AccountRepository */
    private $repository;

    /** @var Projector */
    private $projector;

    public function __construct(AccountRepository $accountRepository, Projector $projector)
    {
        $this->repository = $accountRepository;
        $this->projector = $projector;
    }

    public function handle(UpdateBalanceRequest $command)
    {
        $account = $this->repository->findByEmail($command->getEmail());

        if (!$account) {
            return;
        }

        $account->updateBalance($command->getAmount());

        $this->repository->save($account);
    }
}
