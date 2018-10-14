<?php
namespace App\Tests\Command\Domain\Account;

use App\Command\Domain\Account\AccountWasUpdated;
use DateTimeImmutable;
use MyApp\Command\Domain\Id;
use PHPUnit\Framework\TestCase;
use App\Tests\Builders\Account\BuilderAccountWasUpdated;

/**
 * @group domain
 */
class AccountWasUpdatedTest extends TestCase
{
    const SOURCE_ID = 'asodfijasdf234234';
    const EVENT_ID = 'asodfijasdf234234';

    public function testSerializationOfEvent()
    {
        $date = new DateTimeImmutable();
        $event = new AccountWasUpdated( SELF::EVENT_ID, 'francis.jaa@whatever.com', 12345, $date->setTimestamp(1528324344));

        $serialized = $event->serialize();

        $this->assertEquals(json_encode([
            "eventId" => self::EVENT_ID,
            "amount" => 12345,
            "email" => "francis.jaa@whatever.com",
            "createdAt" => "2018-06-07T00:32:24.000000+02:00",
            "classSource" => 'AccountWasUpdated',
        ]), $serialized);
    }

    public function testUnserializationOfEvent()
    {
        $event = BuilderAccountWasUpdated::any()->build();
        $unserialized = AccountWasUpdated::unserialize($event->serialize());

        $this->assertEquals($event, $unserialized);
    }
}
