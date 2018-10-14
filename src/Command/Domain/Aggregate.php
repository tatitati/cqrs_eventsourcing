<?php
namespace App\Command\Domain;

interface Aggregate
{
    public static function reconstitute(EventStream $stream);

    public function getSnapshot(); // memento pattern here

    public function clearUncommitedEventStream();
}
