<?php
namespace App\Tests\Builders;

interface Builder
{
    public function build();

    public static function any();
}
