<?php
namespace App\Tests\Learning;

use PHPUnit\Framework\TestCase;
use App\Tests\Builders\Account\BuilderAccountWasCreated;

class LearningSerializerTest extends TestCase
{
    const EVENT_ID = 'asodfijasdf234234';

    public function testBasicSerializer()
    {
        $data = BuilderAccountWasCreated::any()->build();

        $serialized = serialize($data);
        $dataRecovered = unserialize($serialized);

        $this->assertInternalType('string', $serialized);
        $this->assertEquals($data, $dataRecovered);
    }
}
