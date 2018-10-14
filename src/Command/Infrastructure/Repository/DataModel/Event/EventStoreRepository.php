<?php
namespace App\Command\Infrastructure\Repository\DataModel\Event;

use DateTimeImmutable;
use App\Command\Domain\EventStream;

interface EventStoreRepository
{
    public function saveStream(EventStream $eventStream): void;

    public function findStream(string $email, ?DateTimeImmutable $since): ?EventStream;
}
