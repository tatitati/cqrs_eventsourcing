<?php
namespace App\Command\ApplicationServices\Account;

use DateTimeImmutable;

class TakeSnapshotRequest
{
    private $email;
    private $requestedTime;

    public function __construct(string $email, DateTimeImmutable $requestedTime)
    {
        $this->email = $email;
        $this->requestedTime = $requestedTime;
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
