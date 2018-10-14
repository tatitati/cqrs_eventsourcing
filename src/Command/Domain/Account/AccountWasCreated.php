<?php
namespace App\Command\Domain\Account;

use App\Command\Domain\Event;
use DateTimeImmutable;
use ReflectionClass;

class AccountWasCreated implements Event
{
    private $eventId;
    private $amount;
    private $email;
    private $createdAt;

    public function __construct($eventId, $email, $amount, DateTimeImmutable $createdAt)
    {
        $this->eventId = $eventId;
        $this->email = $email;
        $this->amount = $amount;
        $this->createdAt = $createdAt;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getSourceId(): string
    {
        return $this->email;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function serialize(): string
    {
        return json_encode([
            'eventId' => $this->getEventId(),
            'amount' => $this->getAmount(),
            'sourceId' => $this->getSourceId(),
            'createdAt' => $this->occurredOn()->format(Event::DATE_FORMAT_EVENT_STORE),
            // we include the type of the event because we might send over the wire this event, and another bounded context might need to know
            // what is exactly the event about; Is an update in the amount?, is the creation of an account?, is resetting the account?
            'classSource' => (new ReflectionClass($this))->getShortName(),
        ]);
    }

    public static function unserialize(string $serializedBodyEvent): AccountWasCreated
    {
        $data = json_decode($serializedBodyEvent, true);
        return new self(
            $data['eventId'],
            $data['sourceId'],
            $data['amount'],
            DateTimeImmutable::createFromFormat(Event::DATE_FORMAT_EVENT_STORE,$data['createdAt'])
        );
    }
}
