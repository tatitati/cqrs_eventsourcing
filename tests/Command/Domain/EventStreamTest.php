<?php
namespace App\Tests\Command\Domain;

use App\Tests\Builders\Account\BuilderAccountWasCreated;
use App\Tests\Builders\Account\BuilderAccountWasDeposited;
use App\Tests\Builders\Account\BuilderAccountWasUpdated;
use PHPUnit\Framework\TestCase;
use App\Tests\Builders\BuilderEventStream;

/**
 * @group domain
 */
class EventStreamTest extends TestCase
{
    public function testCanAddElements()
    {
        $eventStream = BuilderEventStream::anyEmpty()->build();
        $eventStream->add($event1 = BuilderAccountWasCreated::any()->build());
        $eventStream->add($event2 = BuilderAccountWasDeposited::any()->build());

        $this->assertEquals([$event1, $event2], $eventStream->getEvents());
    }
}
