<?php
namespace App\Command\Infrastructure\Projections;

use App\Command\Infrastructure\Projections\Publisher\ProjectionsPublisher;

class Projector
{
    /** @var ProjectionsPublisher */
    private $publisher;

    public function __construct(ProjectionsPublisher $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @param iEvent[]
     * @return string[]
     */
    public function project(array $events): array
    {
        $executedProjections = [];

        foreach($events as $event) {
            $this->publisher->publish($event);

            $executedProjections[] = get_class($event);
        }

        return $executedProjections;
    }
}
