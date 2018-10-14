<?php
namespace App\Command\ApplicationServices\Account;

use App\Command\Domain\Account\Account;
use App\Command\Infrastructure\Projections\Projector;
use App\Command\Infrastructure\Repository\AccountRepository;

class CreateAccount
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

    public function handle(CreateAccountRequest $command)
    {
        if ($this->repository->findByEmail($command->getEmail())) {
            return;
        }

        $account = Account::createAccount($command->getEmail(), $command->getAmount(), $command->getRequestedTime());

        $this->repository->save($account);
    }
}
