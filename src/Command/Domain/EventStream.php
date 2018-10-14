<?php

namespace App\Command\Domain;

class EventStream
{
    /** @var string */
    private $sourceId;

    /** @var Event[]  */
    private $events;

    /**
     * @param Id $aggregateId
     * @param Event[]
     */
    public function __construct($sourceId, array $events = [])
    {
        $this->sourceId = $sourceId;
        $this->events = $events;
    }

    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function add(Event $event)
    {
        $this->events[] = $event;
        return $this;
    }

    public function countEvents(): int
    {
        return count($this->getEvents());
    }
}
