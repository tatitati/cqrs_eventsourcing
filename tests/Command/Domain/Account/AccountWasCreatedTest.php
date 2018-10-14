<?php
namespace App\Tests\Command\Domain\Account;

use App\Command\Domain\Account\AccountWasCreated;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Tests\Builders\Account\BuilderAccountWasCreated;

/**
 * @group domain
 */
class AccountWasCreatedTest extends TestCase
{
    const SOURCE_ID = 'asodfijasdf234234';
    const EVENT_ID = 'asodfijasdf234234';
    const AMOUNT = 12345;

    public function testSerializationOfEvent()
    {
        $date = new DateTimeImmutable();
        $event = new AccountWasCreated(self::EVENT_ID,  self::SOURCE_ID, self::AMOUNT, $date->setTimestamp(1528324344));

        $serialized = $event->serialize();

        $this->assertEquals(json_encode([
            "eventId" => self::EVENT_ID,
            "amount" => 12345,
            "sourceId" => self::SOURCE_ID,
            "createdAt" => "2018-06-07T00:32:24.000000+02:00",
            "classSource" => 'AccountWasCreated',
        ]), $serialized);
    }

    public function testUnserializationOfEvent()
    {
        $event = BuilderAccountWasCreated::any()->build();

        $eventUnserialized = AccountWasCreated::unserialize($event->serialize());

        $this->assertEquals($event, $eventUnserialized);
    }
}
