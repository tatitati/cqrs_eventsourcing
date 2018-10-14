<?php
namespace App\Command\Domain;

class Id
{
    /** string */
    private $value;

    public function __construct(string $guid)
    {
        $this->value = $guid;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
