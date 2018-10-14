<?php
namespace App\Command\Infrastructure\Repository;

use App\Command\Domain\Account\Account;
use App\Command\Infrastructure\Projections\Projector;
use App\Command\Infrastructure\Repository\DataModel\Event\EventStoreRepository;
use App\Command\Infrastructure\Repository\DataModel\Snapshot\SnapshotStoreRepository;

class AccountRepository implements iAccountRepository
{
    /** @var iEventStoreRepository */
    private $eventStore;

    /** @var Projector */
    private $projector;

    /** @var iSnapshotStoreRepository */
    private $snapshotStore;

    public function __construct(EventStoreRepository $eventStore, Projector $projector, SnapshotStoreRepository $snapshotStore)
    {
        $this->eventStore = $eventStore;
        $this->projector = $projector;
        $this->snapshotStore = $snapshotStore;
    }

    public function save(Account $account): void
    {
        $stream = $account->getUncommitedStream();

        $this->eventStore->saveStream($stream);
        $this->projector->project($stream->getEvents());

        $account->clearUncommitedEventStream();
    }

    public function findByEmail(string $email): ?Account
    {
        $account = null;
        $eventStream = null;

        $snapshot = $this->snapshotStore->findSnapshot($email);
        if ($snapshot) {
            $account = Account::restore($snapshot);
            $eventStream = $this->eventStore->findStream($email, $snapshot->getCreatedOn());
        } else {
            $eventStream = $this->eventStore->findStream($email, null);
        }

        if ($eventStream) {
            $account = Account::reconstitute($eventStream, $account);
        }

        return $account;
    }

    public function takeSnapshot(Account $account): void
    {
        $this->snapshotStore->saveSnapshot($account->getSnapshot());
    }
}
