<?php
namespace App\Tests\Command\Infrastructure\Repository\DataModel;

use App\Command\Infrastructure\Repository\DataModel\Event\EventStoreDataModel;
use App\Tests\Builders\Account\BuilderAccountWasCreated;
use App\Tests\Builders\Account\BuilderAccountWasDeposited;
use App\Tests\Builders\Account\BuilderAccountWasUpdated;
use App\Tests\Builders\BuilderEventStream;
use DateTimeImmutable;
use Doctrine\Common\Persistence\ObjectManager;
use App\Command\Infrastructure\Repository\DataModel\Event\EventStoreDbRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group infrastructure
 */
class EventStoreDbRepositoryTest extends KernelTestCase
{
    /**
     * @var EventStoreDbRepository
     */
    private $repository;

    /** @var ObjectManager */
    private $em;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->em->getRepository(EventStoreDataModel::class);

        $this->cleanTable();
    }

    public function testCanFetchEventssSinceAlways()
    {
        $stream = BuilderEventStream::any()
            ->withSourceId('example@email.com')
            ->withEvents([
                $event1 = BuilderAccountWasCreated::any()
                    ->withEmail('example@email.com')
                    ->withCreatedAt(new DateTimeImmutable('1910-10-26 12:13:32'))->build(),
                $event2 = BuilderAccountWasDeposited::any()
                    ->withEmail('example@email.com')
                    ->withCreatedAt(new DateTimeImmutable('1920-10-26 12:13:32'))->build(),
            ])->build();

        $this->repository->saveStream($stream);

        $stream = $this->repository->findStream('example@email.com', null);

        $this->assertEquals(
            BuilderEventStream::any()
                ->withSourceId('example@email.com')
                ->withEvents([$event1, $event2])
                ->build()
            , $stream);
    }

    public function testCanFetchEventsSinceSpecificDate()
    {
        $stream = BuilderEventStream::any()
            ->withSourceId('example@email.com')
            ->withEvents([
                $event1 = BuilderAccountWasCreated::any()
                    ->withEmail('example@email.com')
                    ->withCreatedAt(new DateTimeImmutable('1910-10-26 12:13:32'))->build(),
                $event2 = BuilderAccountWasDeposited::any()
                    ->withEmail('example@email.com')
                    ->withCreatedAt(new DateTimeImmutable('1920-10-26 12:13:32'))->build(),
        ])->build();

        $this->repository->saveStream($stream);

        $stream = $this->repository->findStream(
            'example@email.com',
            new DateTimeImmutable('1915-10-26 12:13:32')
        );

        $this->assertEquals(
            BuilderEventStream::any()
                ->withSourceId('example@email.com')
                ->withEvents([$event2])
                ->build()
            , $stream);
    }

    public function testReturnNullIfNoEventsAreReturned()
    {
        $stream = $this->repository->findStream('nonExistant', null);

        $this->assertNull($stream);
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
            ->delete(EventStoreDataModel::class, 'e')
            ->getQuery()
            ->execute();
    }

}
