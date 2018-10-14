<?php
namespace App\Tests\Builders\Account;

use App\Command\Domain\Account\AccountWasUpdated;
use App\Tests\Builders\Builder;
use DateTimeImmutable;
use Faker;

class BuilderAccountWasUpdated implements Builder
{

    private $eventId;
    private $email;
    private $amount;
    private $createdAt;

    private function __construct()
    {
        $faker = Faker\Factory::create();

        // random values
        $this->eventId = $faker->word . $faker->randomDigit;
        $this->email = $faker->email;
        $this->amount = $faker->numberBetween(10, 120);
        $this->createdAt = new DateTimeImmutable();

    }

    public function withEventId($eventId )
    {
        $this->eventId = $eventId;
        return $this;
    }

    public function withEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function withAmount(int $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function withCreatedAt(DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public static function any()
    {
        return new self();
    }

    public function build(): AccountWasUpdated
    {
        return new AccountWasUpdated(
            $this->eventId,
            $this->email,
            $this->amount,
            $this->createdAt
        );
    }
}
