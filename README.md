composer require php-amqplib/php-amqplib

php artisan make:command ConsumeCommand
php artisan make:command PublishCommand

php artisan rabbitmq:publish
php artisan rabbitmq:consume

RabbitMQ: http://localhost:15672/
