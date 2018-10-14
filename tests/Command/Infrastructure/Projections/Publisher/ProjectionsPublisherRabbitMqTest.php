<?php
namespace App\Tests\Command\Infrastructure\Projections\Publisher;

use App\Command\Domain\Account\AccountWasCreated;
use DateTimeImmutable;
use App\Command\Infrastructure\Projections\Publisher\ProjectionsPublisherRabbitMq;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

/**
 * @group infrastructure
 */
class ProjectionsPublisherRabbitMqTest extends TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('not interested on this');
    }

    public function testCanPublishMessagesToRabbitMq()
    {
        $publisher = new ProjectionsPublisherRabbitMq('localhost', 5672, 'guest', 'guest');

        $publisher->publish($event = $this->anyEventAccountWasCreated());

        $this->assertEquals($event, unserialize($this->consumeMessage()));
    }

    /**
     * Helpers
     */
    private function anyEventAccountWasCreated()
    {
        return new AccountWasCreated(12, 'email@something.com', 20, new DateTimeImmutable());
    }

    private function consumeMessage()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('events', false, false, false, false);

        $consumed = null;
        $callback = function ($msg) use (&$consumed) {
            $consumed =  $msg->body;
        };
        $channel->basic_consume('events', '', false, true, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
            break; // not wait to consume all the messages in the queue, just consume one and stop listening
        }
        $channel->close();
        $connection->close();

        return $consumed;
    }
}
