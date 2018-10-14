<?php
namespace App\Command\ApplicationServices\Account;

use DateTimeImmutable;

class CreateAccountRequest
{
    private $amount;
    private $email;
    private $requestedTime;

    public function __construct(int $amount, string $email)
    {
        $this->amount = $amount;
        $this->email = $email;
        $this->requestedTime = new DateTimeImmutable();
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getRequestedTime(): DateTimeImmutable
    {
        return $this->requestedTime;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
