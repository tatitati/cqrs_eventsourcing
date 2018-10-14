<?php
namespace App\Tests\Command\Presentation;

use App\Tests\Builders\Account\BuilderAccount;
use DateTimeImmutable;
use App\Command\ApplicationServices\Account\TakeSnapshot;
use App\Command\ApplicationServices\Account\TakeSnapshotRequest;
use App\Command\Infrastructure\Repository\AccountRepository;
use PHPUnit\Framework\TestCase;

class TakeSnapshotTest extends TestCase
{
    public function testRepositoryIsUsedProperly()
    {
        $account = BuilderAccount::any()->build();
        $accountRepositoryMock = $this->stubFindByEmailWithOutput($account);

        $accountRepositoryMock
            ->expects($this->once())
            ->method('takeSnapshot')
            ->with($account);

        $appService = new TakeSnapshot($accountRepositoryMock);
        $appService->handle(new TakeSnapshotRequest(
            'something',
            new DateTimeImmutable()
        ));
    }

    private function stubFindByEmailWithOutput($account)
    {
        $accountRepositoryMock = $this->createConfiguredMock(AccountRepository::class, [
            'findByEmail' => $account
        ]);

        return $accountRepositoryMock;
    }
}
