<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Illuminate\Support\Facades\Config;

#[Signature('rabbitmq:publish')]
#[Description('Command description')]
class PublishCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection(
            Config::get('rabbitmq.host'),
            Config::get('rabbitmq.port'),
            Config::get('rabbitmq.username'),
            Config::get('rabbitmq.password')
        );
        $channel = $connection->channel();

        $channel->exchange_declare('laravel', 'fanout', false, true, false);
        $channel->queue_declare('laravel', false, true, false, false, false, new AMQPTable(['x-queue-type' => 'quorum']));

        $channel->queue_bind('laravel', 'laravel');

        $data = [
            'title' => 'some title',
            'content' => 'some content',
        ];

        $data = json_encode($data);

        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg, 'laravel');

        echo " [x] Sent $data'\n";

        $channel->close();
        $connection->close();
    }
}
