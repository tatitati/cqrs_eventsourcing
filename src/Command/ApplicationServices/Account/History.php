<?php
namespace App\Command\ApplicationServices\Account;

use App\Command\Infrastructure\Projections\Projector;
use App\Command\Infrastructure\Repository\AccountRepository;

class History
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

    public function handle(HistoryRequest $command)
    {
        $account = $this->repository->findByEmail($command->getEmail());
        if (!$account) {
            return;
        }
    }
}
