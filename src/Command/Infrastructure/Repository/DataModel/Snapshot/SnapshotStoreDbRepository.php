<?php
namespace App\Command\Infrastructure\Repository\DataModel\Snapshot;

use Doctrine\Common\Persistence\ObjectManager;
use App\Command\Domain\Account\AccountSnapshot;

class SnapshotStoreDbRepository implements SnapshotStoreRepository
{
    private $em;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function saveSnapshot(AccountSnapshot $snapshot): void
    {
        $snapshotDataModel = $this->mapToDataModel($snapshot);

        $this->em->persist($snapshotDataModel);
        $this->em->flush();
    }

    public function findSnapshot(string $sourceId): ?AccountSnapshot
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e')
            ->from(SnapshotStoreDataModel::class, 'e')
            ->where('e.sourceId=:email')
            ->addOrderBy('e.occurredOn', 'ASC')
            ->addOrderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('email', $sourceId );

        $snapshotDataModel = $qb->getQuery()->getOneOrNullResult();

        if (!$snapshotDataModel) {
            return null;
        }

        return $this->mapToDomainModel($snapshotDataModel);
    }

    private function mapToDataModel(AccountSnapshot $snapshot): SnapshotStoreDataModel
    {
        return new SnapshotStoreDataModel(
            $snapshot->getEmail(),
            $snapshot->getCreatedOn(),
            $snapshot->serialize()
        );
    }

    private function mapToDomainModel(SnapshotStoreDataModel $eventsDataModel): AccountSnapshot
    {
        return AccountSnapshot::unserialize($eventsDataModel->getBodySnapshot());
    }
}
