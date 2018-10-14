<?php
namespace App\Tests\Command\Infrastructure\Repository\DataModel;

use Doctrine\Common\Persistence\ObjectManager;
use App\Command\Infrastructure\Repository\DataModel\Snapshot\SnapshotStoreDataModel;
use App\Command\Infrastructure\Repository\DataModel\Snapshot\SnapshotStoreDbRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Tests\Builders\Account\BuilderAccountSnapshot;

/**
 * @group infrastructure
 */
class SnapshotStoreDbRepositoryTest extends KernelTestCase
{
    /**
     * @var SnapshotStoreDbRepository
     */
    private $repository;

    /** @var ObjectManager */
    private $em;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->em->getRepository(SnapshotStoreDataModel::class);        

        $this->cleanTable();
    }

    public function testCanSaveDataAndRead()
    {
        $snapshot = BuilderAccountSnapshot::any()
            ->withEmail('snapshot@email.com')
            ->withAmount(110)
            ->build();


        $this->repository->saveSnapshot($snapshot);
        $snapshot = $this->repository->findSnapshot('snapshot@email.com');

        $this->assertEquals($snapshot, $snapshot);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    private function cleanTable()
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->delete(SnapshotStoreDataModel::class, 'e')
            ->getQuery()
            ->execute();
    }
}
