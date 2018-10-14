<?php
namespace App\Tests\Command\Domain\Account;

use App\Command\Domain\Account\Account;
use App\Command\Domain\Account\AccountWasCreated;
use App\Command\Domain\Account\AccountWasUpdated;
use App\Command\Domain\EventStream;
use App\Command\Domain\Event;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Tests\Builders\Account\BuilderAccountSnapshot;
use App\Tests\Builders\Account\BuilderAccountWasUpdated;
use App\Tests\Builders\Account\BuilderAccountWasCreated;
use App\Tests\Builders\BuilderEventStream;

/**
 * @group domain
 */
class AccountTest extends TestCase
{
    private const EMAIL = 'francis.jaa@gmail.com';
    private const INITIAL_AMOUNT = 25;

    public function testCreation()
    {
        $newAccount = Account::createAccount( self::EMAIL, self::INITIAL_AMOUNT);

        $this->assertEquals(self::EMAIL, $newAccount->getEmail());
        $this->assertSame(self::INITIAL_AMOUNT, $newAccount->getAmount());

        $this->assertInstanceOf(Account::class,
            $newAccount,
            'An account domain must be created'
        );

        $this->assertInstanceOf(EventStream::class,
            $newAccount->getUncommitedStream(),
            'The created account must have an Event stream'
        );

        $this->assertContainsOnlyInstancesOf(AccountWasCreated::class,
            $newAccount->getUncommitedStream()->getEvents(),
            'The stream must have the event AccountWasCreated'
        );
    }

    public function testUpdateBalanceAccount()
    {
        $account=  Account::createAccount(self::EMAIL, self::INITIAL_AMOUNT)
            ->updateBalance(20);

        $events = $account->getUncommitedStream()->getEvents();

        $this->assertCount(2, $events);
        $this->assertContainsOnlyInstancesOf(Event::class, $events);
        $this->assertInstanceOf(AccountWasCreated::class, $events[0]);
        $this->assertInstanceOf(AccountWasUpdated::class, $events[1]);
    }

    public function testReconstituteStream()
    {
        $eventStream = BuilderEventStream::any()->withSourceId(self::EMAIL)->withEvents([
            BuilderAccountWasCreated::any()->withAmount(25)->build(),
            BuilderAccountWasUpdated::any()->withAmount(10)->build()
        ])->build();

        $reBuiltAccount = Account::reconstitute($eventStream);

        $this->assertEquals(self::EMAIL, $reBuiltAccount->getEmail());
        $this->assertEquals(35, $reBuiltAccount->getAmount());
        $this->assertEquals(0, $reBuiltAccount->getUncommitedStream()->countEvents());
    }

    /**
     * [Snapshot support]
     */
    public function testAccountCanExportSnapshot()
    {
        $newAccount = Account::createAccount( self::EMAIL, self::INITIAL_AMOUNT);
        $snapshot = $newAccount->getSnapshot();

        $this->assertEquals(self::EMAIL, $snapshot->getEmail());
        $this->assertEquals(self::INITIAL_AMOUNT, $snapshot->getAmount());
        $this->assertInstanceOf(DateTimeImmutable::class, $snapshot->getCreatedOn());
    }

    public function testAccountCanRestoreFromSnapshot()
    {
        $givenSnapshot = BuilderAccountSnapshot::any()
            ->withEmail(self::EMAIL)
            ->withAmount(self::INITIAL_AMOUNT)
            ->build();

        $account = Account::restore($givenSnapshot);

        $this->assertEquals(self::EMAIL, $account->getEmail());
        $this->assertEquals(self::INITIAL_AMOUNT, $account->getAmount());
        $this->assertEquals([], $account->getUncommitedStream()->getEvents());
    }

    public function testStreamCanReconstituteOverSnapshot()
    {
        $givenSnapshot = BuilderAccountSnapshot::any()
            ->withEmail(self::EMAIL)
            ->withAmount(5)
            ->build();

        $eventStream = BuilderEventStream::any()->withSourceId(self::EMAIL)->withEvents([
            BuilderAccountWasUpdated::any()->withAmount(10)->build(),
            BuilderAccountWasUpdated::any()->withAmount(20)->build(),
        ])->build();

        $account = Account::restore($givenSnapshot);
        $account = Account::reconstitute($eventStream, $account);

        $this->assertEquals(self::EMAIL, $account->getEmail());
        $this->assertEquals(35, $account->getAmount());
        $this->assertEquals([], $account->getUncommitedStream()->getEvents());
    }
}
