<?php
namespace App\Tests\Command\Infrastructure\Repository;

use App\Command\Domain\Account\AccountSnapshot;
use App\Command\Infrastructure\Projections\Projector;
use App\Command\Infrastructure\Repository\AccountRepository;
use App\Command\Infrastructure\Repository\DataModel\Event\EventStoreRepository;
use App\Command\Infrastructure\Repository\DataModel\Snapshot\SnapshotStoreRepository;
use PHPUnit\Framework\TestCase;
use App\Tests\Builders\Account\BuilderAccount;

/**
 * @group infrastructure
 */
class AccountRepositoryTakeSnapshotTest extends TestCase
{
    public function testTakeSnapshot()
    {
        $account = BuilderAccount::any()->build();

        $dummyEventStore = $this->createMock(EventStoreRepository::class);
        $dummyProjector = $this->createMock(Projector::class);

        $mockSnapshotStore = $this->createMock(SnapshotStoreRepository::class);
        $mockSnapshotStore
            ->expects($this->once())
            ->method('saveSnapshot')
            ->with($this->isInstanceOf(AccountSnapshot::class));

        (new AccountRepository($dummyEventStore, $dummyProjector, $mockSnapshotStore))
            ->takeSnapshot($account);
    }
}
