<?php

declare(strict_types=1);

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * @author Vitaliy Bilotil <bilotilvv@gmail.com>
 */
class LoggerConsumerCallback implements ConsumerInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return int|bool One of ConsumerInterface::MSG_* constants according to callback outcome, or false otherwise.
     */
    public function execute(AMQPMessage $msg)
    {
        $keys = explode('.', $msg->getRoutingKey());
        $prefix = $keys[1];
        $body = json_decode($msg->getBody(), true);

            if(str_contains($msg->getRoutingKey(), $prefix)){         //вариант реализации:
                $this->logger->log(                                   //стр.32 -   $this->logger->$prefix(
                    $prefix,                                          //стр.33 -   пустая
                    'Message: ' . $body['message'] . '!'
                );
            }
        return ConsumerInterface::MSG_ACK;
    }
}
