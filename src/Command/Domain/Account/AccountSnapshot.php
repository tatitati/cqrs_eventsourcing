<?php
namespace App\Command\Domain\Account;

use App\Command\Domain\Event;
use App\Command\Domain\Snapshot;
use DateTimeImmutable;

class AccountSnapshot implements Snapshot
{
    private $email;
    private $amount;
    private $createdOn;

    public function __construct($email, $amount, DateTimeImmutable $createdOn)
    {
        $this->email = $email;
        $this->amount = $amount;
        $this->createdOn = $createdOn;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCreatedOn(): DateTimeImmutable
    {
        return $this->createdOn;
    }

    public function serialize()
    {
        return json_encode([
            'email' => $this->email,
            'amount' => $this->amount,
            'created_on' => $this->createdOn->format(Event::DATE_FORMAT_EVENT_STORE),
        ]);
    }

    public static function unserialize(string $json): AccountSnapshot
    {
        $data = json_decode($json, true);

        return new self(
            $data['email'],
            $data['amount'],
            DateTimeImmutable::createFromFormat(Event::DATE_FORMAT_EVENT_STORE,$data['created_on'])
        );
    }
}
