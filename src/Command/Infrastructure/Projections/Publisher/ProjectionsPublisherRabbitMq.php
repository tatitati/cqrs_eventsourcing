<?php
namespace App\Command\Infrastructure\Projections\Publisher;

use App\Command\Domain\Event;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ProjectionsPublisherRabbitMq implements ProjectionsPublisher
{
    /** @var AMQPStreamConnection */
    private $connection;

    public function __construct($host, $port, $user, $pass)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
    }

    public function publish(Event $event)
    {
        $msg = serialize($event);


        $channel = $this->connection->channel();
        $channel->queue_declare('events', false, false, false, false);

        $msg = new AMQPMessage($msg);

        $channel->basic_publish($msg, '', 'events');
        $channel->close();
        $this->connection->close();
    }
}
