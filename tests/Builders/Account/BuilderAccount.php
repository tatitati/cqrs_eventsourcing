<?php
namespace App\Tests\Builders\Account;

use App\Command\Domain\Account\Account;
use App\Tests\Builders\Builder;
use Faker;

class BuilderAccount implements Builder
{
    private $email;
    private $amount;

    public function __construct()
    {
        $faker = Faker\Factory::create();

        $this->email = $faker->email;
        $this->amount = $faker->numberBetween(10, 120);
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

    public static function any()
    {
        return new self();
    }

    public function build(): Account
    {
        return Account::createAccount(
            $this->email,
            $this->amount
        );
    }
}
