<?php
namespace App\Tests\Command\Domain\Account;

use App\Command\Domain\Account\AccountWasDeposited;
use App\Tests\Builders\Account\BuilderAccountWasDeposited;
use DateTimeImmutable;
use InvalidArgumentException;
use MyApp\Command\Domain\Id;
use PHPUnit\Framework\TestCase;

/**
 * @group domain
 */
class AccountWasDepositedTest extends TestCase
{
    const SOURCE_ID = 'asodfijasdf234234';
    const EVENT_ID = 'asodfijasdf234234';

    /**
     * @expectedException InvalidArgumentException
     */
    public function testZeroDepositeAreNotValid()
    {
        $this->event(0);
    }

    public function testSerializationOfEvent()
    {
        $event = $this->event(12345);

        $serialized = $event->serialize();

        $this->assertEquals(json_encode([
            "eventId" => self::EVENT_ID,
            "amount" => 12345,
            "email" => "francis.jaa@whatever.com",
            "createdAt" => "2018-06-07T00:32:24.000000+02:00",
            "classSource" => 'AccountWasDeposited',
        ]), $serialized);
    }

    public function testUnserializationOfEvent()
    {
        $event = BuilderAccountWasDeposited::any()->build();
        $unserialized = AccountWasDeposited::unserialize($event->serialize());

        $this->assertEquals($event, $unserialized);
    }

    private function event($amount)
    {
        $date = new DateTimeImmutable();
        return new AccountWasDeposited( SELF::EVENT_ID, 'francis.jaa@whatever.com', $amount, $date->setTimestamp(1528324344));
    }
}
