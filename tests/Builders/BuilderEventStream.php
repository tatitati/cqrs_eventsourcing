<?php
namespace App\Tests\Builders;

use App\Command\Domain\EventStream;
use App\Tests\Builders\Account\BuilderAccountWasCreated;
use App\Tests\Builders\Account\BuilderAccountWasDeposited;
use Faker;

class BuilderEventStream implements Builder
{
    private $id;
    private $events;

    public function __construct()
    {
        $faker = Faker\Factory::create();

        // random values
        $this->id = $faker->word;
        $this->events = [
            BuilderAccountWasCreated::any()->build(),
            BuilderAccountWasDeposited::any()->build(),
            BuilderAccountWasDeposited::any()->build(),
        ];
    }

    public function withSourceId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    public function withEvents(array $events)
    {
        $this->events = $events;
        return $this;
    }

    public static function any()
    {
        return new self();
    }

    public static function anyEmpty()
    {
        $self = new Self();
        $self->withEvents([]);
        return $self;
    }

    public function build(): EventStream
    {
        return new EventStream(
            $this->id,
            $this->events
        );
    }
}
