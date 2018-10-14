<?php
namespace App\Command\Infrastructure\Projections\Publisher;

use App\Command\Domain\Event;

interface ProjectionsPublisher
{
    public function publish(Event $event);
}
