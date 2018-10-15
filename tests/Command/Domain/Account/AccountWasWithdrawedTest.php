<?php
namespace App\Tests\Command\Domain\Account;

use App\Command\Domain\Account\AccountWasWithdrawed;
use App\Tests\Builders\Account\BuilderAccountWasWithdrawed;
use DateTimeImmutable;
use InvalidArgumentException;
use MyApp\Command\Domain\Id;
use PHPUnit\Framework\TestCase;

/**
 * @group domain
 */
class AccountWasWithdrawedTest extends TestCase
{
    const SOURCE_ID = 'asodfijasdf234234';
    const EVENT_ID = 'asodfijasdf234234';

    public function testAmountWithdrawedIsExpressedAsAPositiveNumber()
    {
        $event1 = $this->event(-1212);
        $event2 = $this->event(3232);

        $this->assertEquals(1212, $event1->getAmount());
        $this->assertEquals(3232, $event2->getAmount());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThatZeroWithdrawalAreNotValid()
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
            "classSource" => 'AccountWasWithdrawed',
        ]), $serialized);
    }

    public function testUnserializationOfEvent()
    {
        $event = BuilderAccountWasWithdrawed::any()->build();
        $unserialized = AccountWasWithdrawed::unserialize($event->serialize());

        $this->assertEquals($event, $unserialized);
    }

    private function event($amount)
    {
        $date = new DateTimeImmutable();
        return new AccountWasWithdrawed( SELF::EVENT_ID, 'francis.jaa@whatever.com', $amount, $date->setTimestamp(1528324344));
    }
}
