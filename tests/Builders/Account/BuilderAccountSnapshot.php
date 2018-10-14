<?php
namespace App\Tests\Builders\Account;

use App\Command\Domain\Account\AccountSnapshot;
use App\Tests\Builders\Builder;
use DateTimeImmutable;
use Faker;

class BuilderAccountSnapshot implements Builder
{
    private $email;
    private $amount;
    private $createdOn;

    private function __construct()
    {
        $faker = Faker\Factory::create();

        $this->email = $faker->email;
        $this->amount = $faker->numberBetween(10, 120);
        $this->createdOn = new DateTimeImmutable();
    }

    public function build()
    {
        return new AccountSnapshot(
            $this->email,
            $this->amount,
            $this->createdOn
        );
    }

    public static function any()
    {
        return new self();
    }

    public function withEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function withAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function withCreatedOn(DateTimeImmutable $createdOn)
    {
        $this->createdOn = $createdOn;
        return $this;
    }
}
