<?php
namespace App\Tests\Command\Infrastructure\Repository;

use App\Command\Domain\Account\Account;
use App\Command\Infrastructure\Repository\AccountRepository;
use App\Command\Infrastructure\Repository\DataModel\Event\EventStoreRepository;
use App\Command\Infrastructure\Projections\Projector;
use App\Command\Infrastructure\Repository\DataModel\Snapshot\SnapshotStoreRepository;
use PHPUnit\Framework\TestCase;

/**
 * @group infrastructure
 */
class AccountRepositorySaveTest extends TestCase
{
    private const EMAIL = 'francis.jaa@gmail.com';
    private const AMOUNT = 100;

    public function testProjectionsNeedAnArrayOfEvents()
    {
        $dummy = $this->createMock(EventStoreRepository::class);
        $dummySnapshotStore = $this->createMock(SnapshotStoreRepository::class);

        $account = Account::createAccount(self::EMAIL, self::AMOUNT);

        $projectorMock = $this->createMock(Projector::class);
        $projectorMock
            ->expects($this->once())
            ->method('project')
            ->with($account->getUncommitedStream()->getEvents());

        (new AccountRepository($dummy, $projectorMock, $dummySnapshotStore))->save($account);
    }

    public function testStoreNeedsAnEventStream()
    {
        $dummy = $this->createMock(Projector::class);
        $dummySnapshotStore = $this->createMock(SnapshotStoreRepository::class);

        $account = Account::createAccount( self::EMAIL, self::AMOUNT);

        $redisMock = $this->createMock(EventStoreRepository::class);
        $redisMock
            ->expects($this->once())
            ->method('saveStream')
            ->with($account->getUncommitedStream());

        (new AccountRepository($redisMock, $dummy, $dummySnapshotStore))->save($account);
    }

    public function testAfterSavingAllRecordedEventsAreCleaned()
    {
        $dummy1 = $this->createMock(EventStoreRepository::class);
        $dummy2 = $this->createMock(Projector::class);
        $dummySnapshotStore = $this->createMock(SnapshotStoreRepository::class);

        $account = Account::createAccount(self::EMAIL, self::AMOUNT);
        $account->updateBalance(10);
        $account->updateBalance(7);

        $this->assertEquals(3, $account->getUncommitedStream()->countEvents());
        (new AccountRepository($dummy1, $dummy2, $dummySnapshotStore))->save($account);
        $this->assertEquals(0, $account->getUncommitedStream()->countEvents());
    }


}
