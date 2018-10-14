<?php
namespace App\Tests\Command\Infrastructure\Repository;

use App\Command\Domain\Account\Account;
use App\Command\Infrastructure\Repository\AccountRepository;
use App\Command\Infrastructure\Repository\DataModel\Event\EventStoreRepository;
use App\Command\Infrastructure\Projections\Projector;
use App\Command\Infrastructure\Repository\DataModel\Snapshot\SnapshotStoreRepository;
use App\Tests\Builders\BuilderEventStream;
use PHPUnit\Framework\TestCase;
use App\Tests\Builders\Account\BuilderAccountSnapshot;

/**
 * @group infrastructure
 */
class AccountRepositoryByAccountIdTest extends TestCase
{
    private const EMAIL = 'ANY@ASDFASFD.COM';

    /**
     * - Account Repo interaction with Event store
     */
    public function testEventStoreIsUsedProperly()
    {
        $dummyProjector = $this->createMock(Projector::class);
        $dummySnapshotStore = $this->createMock(SnapshotStoreRepository::class);
        $mockEventStore = $this->createMock(EventStoreRepository::class);
        $mockEventStore
            ->expects($this->once())
            ->method('findStream')
            ->with(SELF::EMAIL, null);

        (new AccountRepository($mockEventStore, $dummyProjector, $dummySnapshotStore))
            ->findByEmail(SELF::EMAIL);
    }

    public function testRepositoryCanReconstituteAnEventStream()
    {
        $dummyProjector = $this->createMock(Projector::class);
        $dummySnapshotStore = $this->createMock(SnapshotStoreRepository::class);
        $eventStoreStub = $this->createConfiguredMock(EventStoreRepository::class, [
            'findStream' => BuilderEventStream::any()->withSourceId(SELF::EMAIL)->build()
        ]);

        $account = (new AccountRepository($eventStoreStub, $dummyProjector, $dummySnapshotStore))->findByEmail(SELF::EMAIL);

        $this->assertInstanceOf(Account::class, $account);
    }

    /**
     * - Account Repo interaction with Snapshot store
     */
    public function testAccountRepoTryToFindSnapshot()
    {
        $dummyEventStore = $this->createMock(EventStoreRepository::class);
        $dummyProjector = $this->createMock(Projector::class);
        $mockSnapshotStore = $this->createMock(SnapshotStoreRepository::class);
        $mockSnapshotStore
            ->expects($this->once())
            ->method('findSnapshot')
            ->with(SELF::EMAIL);

        (new AccountRepository($dummyEventStore, $dummyProjector, $mockSnapshotStore))->findByEmail(SELF::EMAIL);
    }

    public function testAccountCanBeCreatedInRepoFromSnapshot()
    {
        $dummyEventStore = $this->createMock(EventStoreRepository::class);
        $dummyProjector = $this->createMock(Projector::class);
        $stubSnapshotStore = $this->createConfiguredMock(SnapshotStoreRepository::class, [
            'findSnapshot' => BuilderAccountSnapshot::any()->build()
        ]);

        $account = (new AccountRepository($dummyEventStore, $dummyProjector, $stubSnapshotStore))->findByEmail(SELF::EMAIL);

        $this->assertInstanceOf(Account::class, $account);
    }

    /**
     * - Event Stream try to fetch events since the last snapshot date
     */
    public function testEventStoreReceiveDateFromSnapshot()
    {
        $dummyProjector = $this->createMock(Projector::class);
        $snapshot = BuilderAccountSnapshot::any()->build();
        $stubSnapshotStore = $this->createConfiguredMock(SnapshotStoreRepository::class, [
            'findSnapshot' => $snapshot
        ]);

        $mockEventStore = $this->createMock(EventStoreRepository::class);
        $mockEventStore
            ->expects($this->once())
            ->method('findStream')
            ->with(SELF::EMAIL, $snapshot->getCreatedOn());

        (new AccountRepository($mockEventStore, $dummyProjector, $stubSnapshotStore))->findByEmail(SELF::EMAIL);
    }
}
