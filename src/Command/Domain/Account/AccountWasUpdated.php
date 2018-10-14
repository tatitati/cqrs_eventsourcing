<?php
namespace App\Command\Domain\Account;

use App\Command\Domain\Event;
use MyApp\Command\Domain\Id;
use DateTimeImmutable;
use ReflectionClass;

class AccountWasUpdated implements Event
{
    private $eventId;
    private $email;
    private $amount;
    private $ocurredOn;

    public function __construct($eventId, $email, $amount, DateTimeImmutable $ocurredOn)
    {
        $this->eventId = $eventId;
        $this->email = $email;
        $this->amount = $amount;
        $this->ocurredOn = $ocurredOn;
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
        return $this->ocurredOn;
    }

    public function serialize(): string
    {
        return json_encode([
            'eventId' => $this->getEventId(),
            'amount' => $this->getAmount(),
            'email' => $this->getSourceId(),
            'createdAt' => $this->occurredOn()->format(Event::DATE_FORMAT_EVENT_STORE),
            'classSource' => (new ReflectionClass($this))->getShortName()
        ]);
    }

    public static function unserialize(string $serializedBodyEvent): AccountWasUpdated
    {
        $data = json_decode($serializedBodyEvent, true);

        return new self(
            $data['eventId'],
            $data['email'],
            $data['amount'],
            DateTimeImmutable::createFromFormat(Event::DATE_FORMAT_EVENT_STORE,$data['createdAt'])
        );
    }
}
