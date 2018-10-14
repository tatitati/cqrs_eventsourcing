<?php
namespace App\Command\Infrastructure\Repository\DataModel\Event;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EventStoreDbRepository")
 * @ORM\Table(name="event_store")
 */
class EventStoreDataModel
{
    public function __construct($sourceId, $occurredOn, $bodyEvent, $eventType)
    {
        $this->sourceId = $sourceId;
        $this->eventType = $eventType;
        $this->occurredOn = $occurredOn;
        $this->bodyEvent = $bodyEvent;
    }

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", name="source_id") **/
    protected $sourceId;

    /** @ORM\Column(type="string", name="event_type") **/
    protected $eventType;

    /** @ORM\Column(type="datetime", name="occurred_on") **/
    protected $occurredOn;

    /** @ORM\Column(type="string", name="body_event") **/
    protected $bodyEvent;

    public function getBodyEvent()
    {
        return $this->bodyEvent;
    }

    public function getEventType()
    {
        return $this->eventType;
    }
}
