<?php
namespace App\Tests\Learning;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class LearningRabbitMqTest extends TestCase
{
    public function testBasicPublish()
    {
        $this->givenPublishedMessage('Hello World!');

        $consumed = $this->consumeMessage();

        $this->assertEquals('Hello World!', $consumed);
    }

    private function givenPublishedMessage()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);
        $msg = new AMQPMessage('Hello World!');
        $channel->basic_publish($msg, '', 'hello');
        $channel->close();
        $connection->close();
    }

    private function consumeMessage()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);

        $consumed = null;
        $callback = function ($msg) use (&$consumed) {
           $consumed =  $msg->body;
        };
        $channel->basic_consume('hello', '', false, true, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
            break; // not wait to consume all the messages in the queue, just consume one and stop listening
        }
        $channel->close();
        $connection->close();

        return $consumed;
    }
}
