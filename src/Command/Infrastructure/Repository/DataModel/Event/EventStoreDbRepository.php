<?php
namespace App\Command\Infrastructure\Repository\DataModel\Event;

use App\Command\Domain\Account\AccountWasDeposited;
use App\Command\Domain\Account\AccountWasWithdrawed;
use DateTimeImmutable;
use Doctrine\Common\Persistence\ObjectManager;
use App\Command\Domain\Account\AccountWasCreated;
use App\Command\Domain\Account\AccountWasUpdated;
use App\Command\Domain\EventStream;
use App\Command\Domain\iEvent;
use ReflectionClass;

class EventStoreDbRepository implements EventStoreRepository
{
    private $em;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function saveStream(EventStream $eventStream): void
    {
        $eventsDataModel = $this->mapToDataModel($eventStream->getEvents());

        foreach ($eventsDataModel as $eventDataModel) {
            $this->em->persist($eventDataModel);
        }

        $this->em->flush();
    }

    public function findStream(string $email, ?DateTimeImmutable $since): ?EventStream
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e')
            ->from(EventStoreDataModel::class, 'e')
            ->where('e.sourceId=:email')
            ->addOrderBy('e.occurredOn', 'ASC')
            ->addOrderBy('e.id', 'ASC')
            ->setParameter('email', $email);

        if ($since) {
            $qb->andWhere('e.occurredOn > :since')->setParameter('since', $since);
        }

        $eventsDataModel = $qb->getQuery()->getResult();

        if (!$eventsDataModel) {
            return null;
        }

        $eventsDomainModel = $this->mapToDomainModel($eventsDataModel);

        return new EventStream($email, $eventsDomainModel);
    }

    private function mapToDataModel(array $events)
    {
        $eventsDataModel = [];

        /** @var iEvent $event */
        foreach ($events as $event) {
            $eventsDataModel[] = new EventStoreDataModel(
                $event->getSourceId(),
                $event->occurredOn(),
                $event->serialize(),
                (new ReflectionClass($event))->getShortName()
            );
        }

        return $eventsDataModel;
    }

    /**
     * @param EventStoreDataModel[] $eventsDataModel
     * @return iEvent[]
     */
    private function mapToDomainModel(array $eventsDataModel): array
    {
        $eventsDomainModel = [];
        foreach ($eventsDataModel as $event) {
            $method = 'unserial' . $event->getEventType();
            $eventsDomainModel[] = $this->$method($event);
        }

        return $eventsDomainModel;
    }

    private function unserialAccountWasCreated(EventStoreDataModel $event): AccountWasCreated
    {
        return AccountWasCreated::unserialize($event->getBodyEvent());
    }

    private function unserialAccountWasDeposited(EventStoreDataModel $event): AccountWasDeposited
    {
        return AccountWasDeposited::unserialize($event->getBodyEvent());
    }

    private function unserialAccountWasWithdrawed(EventStoreDataModel $event): AccountWasWithdrawed
    {
        return AccountWasWithdrawed::unserialize($event->getBodyEvent());
    }
}
