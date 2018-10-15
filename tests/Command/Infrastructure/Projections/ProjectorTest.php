<?php
namespace App\Tests\Command\Infrastructure\Projections;

use App\Command\Infrastructure\Projections\Projector;

use App\Command\Infrastructure\Projections\Publisher\ProjectionsPublisher;
use App\Tests\Builders\Account\BuilderAccountWasDeposited;
use PHPUnit\Framework\TestCase;
use App\Tests\Builders\Account\BuilderAccountWasCreated;
use App\Tests\Builders\Account\BuilderAccountWasUpdated;

/**
 * @group infrastructure
 */
class ProjectorTest extends TestCase
{
    public function testProjectorPublishAllPassedEvents()
    {
        $events = [
            $event1 = BuilderAccountWasCreated::any()->withEventId('AASDSD34')->build(),
            $event2 = BuilderAccountWasDeposited::any()->withEventId('3432DDDD')->build(),
            $event3 = BuilderAccountWasDeposited::any()->withEventId('99asdfas')->build(),
        ];

        $mockPublisher = $this->createMock(ProjectionsPublisher::class);
        $mockPublisher
            ->expects($this->exactly(3))
            ->method('publish')
            ->withConsecutive(
                [$event1],
                [$event2],
                [$event3]
            );


        (new Projector($mockPublisher))->project($events);
    }
}
