<?php
namespace App\Tests\Command\Domain\Account;

use App\Command\Domain\Account\AccountSnapshot;
use App\Tests\Builders\Account\BuilderAccountSnapshot;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @group domain
 */
class AccountSnapshotTest extends TestCase
{
    const EMAIL = 'anemail@something.com';
    const AMOUNT = 323;

    public function testSerialize()
    {
        $date = new DateTimeImmutable();
        $time = $date->setTimestamp(1528324344);


        $snapshot = BuilderAccountSnapshot::any()
            ->withEmail(self::EMAIL)
            ->withAmount(self::AMOUNT)
            ->withCreatedOn($time)
            ->build();

        $this->assertEquals(json_encode([
                'email' => self::EMAIL,
                'amount' => self::AMOUNT,
                "created_on" => "2018-06-07T00:32:24.000000+02:00",
            ]),
            $snapshot->serialize()
        );
    }

    public function testUnserialize()
    {
        $snapshot = BuilderAccountSnapshot::any()->build();

        $unserialized = AccountSnapshot::unserialize($snapshot->serialize());

        $this->assertEquals($snapshot, $unserialized);
    }
}
