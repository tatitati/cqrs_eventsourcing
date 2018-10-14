<?php
namespace App\Command\Domain\Account;

use App\Command\Domain\EventStream;
use App\Command\Domain\Aggregate;
use App\Command\Domain\Event;
use App\Command\Domain\Snapshot;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use ReflectionClass;

class Account implements Aggregate
{
    private $email;
    private $amount;
    private $recordedEvents;

    private function __construct($email, $amount)
    {
        $this->email = $email;
        $this->amount = $amount;
        $this->recordedEvents = new EventStream($email);
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSnapshot(): AccountSnapshot
    {
        return new AccountSnapshot($this->getEmail(), $this->getAmount(), new DateTimeImmutable());
    }

    public function getUncommitedStream(): EventStream
    {
        return $this->recordedEvents;
    }

    public static function createAccount($email, $amount)
    {
        $account =  new static($email, 0);

        $account->recordAndApplyEvent(
            new AccountWasCreated(Uuid::uuid4()->toString(), $email, $amount, new DateTimeImmutable())
        );

        return $account;
    }

    public function updateBalance(int $newAmount)
    {
        $this->recordAndApplyEvent(
            new AccountWasUpdated(Uuid::uuid4()->toString(), $this->email, $newAmount, new DateTimeImmutable())
        );

        return $this;
    }

    public static function reconstitute(EventStream $stream, $account = null)
    {
        if (!$account) {
            $account = new Static($stream->getSourceId(), 0);
        }

        $events = $stream->getEvents();

        foreach ($events as $event) {
            $account->applyEvent($event);
        }

        return $account;
    }

    public static function restore(Snapshot $snapshot)
    {
        return new self(
            $snapshot->getEmail(),
            $snapshot->getAmount()
        );
    }

    public function clearUncommitedEventStream()
    {
        $this->recordedEvents = new EventStream($this->email);
    }

    private function recordAndApplyEvent(Event $event)
    {
        $this->recordEvent($event);
        $this->applyEvent($event);
    }

    private function recordEvent(Event $event)
    {
        $this->recordedEvents->add($event);
    }

    private function applyEvent(Event $event)
    {
        $applyMethod = 'apply' . (new ReflectionClass($event))->getShortName();
        $this->$applyMethod($event);
    }

    private function applyAccountWasUpdated(AccountWasUpdated $event)
    {
        $this->amount = $this->amount + $event->getAmount();
    }

    private function applyAccountWasCreated(AccountWasCreated $event)
    {
        $this->amount = $this->amount + $event->getAmount();
    }
}
