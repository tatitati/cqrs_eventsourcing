<?php
namespace App\Tests\Builders;

use Faker;

class BuilderGuid implements Builder
{
    private $guid;

    public function __construct()
    {
        $faker = Faker\Factory::create();

        $this->guid = $faker->word;
    }

    public function anyWithGuid(string $guid)
    {
        $this->guid = new Guid($guid);
    }

    public function build()
    {
        return new Guid($this->guid);
    }

    public function any()
    {
        return new self();
    }
}
