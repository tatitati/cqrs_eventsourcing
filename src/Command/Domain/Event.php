<?php
namespace App\Command\Domain;

use DateTimeImmutable;

interface Event
{
    public const DATE_FORMAT_EVENT_STORE = "Y-m-d\TH:i:s.uP";

    public function getEventId();

    public function getSourceId();

    public function occurredOn(): DateTimeImmutable;

    public function serialize(): string;

    public static function unserialize(string $jsonEvent);
}

