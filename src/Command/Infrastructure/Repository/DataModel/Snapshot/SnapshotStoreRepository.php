<?php
namespace App\Command\Infrastructure\Repository\DataModel\Snapshot;

use App\Command\Domain\Account\AccountSnapshot;

interface SnapshotStoreRepository
{
    public function saveSnapshot(AccountSnapshot $account): void;

    public function findSnapshot(string $sourceId): ?AccountSnapshot;
}
