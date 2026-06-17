<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

#[Signature('rabbitmq:consume')]
#[Description('Command description')]
class ConsumeCommand extends Command
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

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function (AMQPMessage $msg) {
            $data = json_decode($msg->body, true);
            print_r($data);
        };

        $channel->basic_consume('laravel', '', false, true, false, false, $callback);

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }
}
