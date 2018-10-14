<?php
namespace App\Command\Infrastructure\Repository\DataModel\Snapshot;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SnapshotStoreDbRepository")
 * @ORM\Table(name="snapshot_store")
 */
class SnapshotStoreDataModel
{
    public function __construct($sourceId, $occurredOn, $bodySnapshot)
    {
        $this->sourceId = $sourceId;
        $this->occurredOn = $occurredOn;
        $this->bodySnapshot = $bodySnapshot;
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

    /** @ORM\Column(type="datetime", name="occurred_on") **/
    protected $occurredOn;

    /** @ORM\Column(type="string", name="body_snapshot") **/
    protected $bodySnapshot;

    public function getBodySnapshot()
    {
        return $this->bodySnapshot;
    }
}
