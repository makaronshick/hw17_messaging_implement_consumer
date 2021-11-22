<?php

declare(strict_types=1);

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * @author Vitaliy Bilotil <bilotilvv@gmail.com>
 */
class NewEntriesNotifierConsumerCallback implements ConsumerInterface
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

        if(str_contains($msg->getRoutingKey(), $prefix)) {
            $this->logger->info(                                          //по заданию нужно тут применить функцию mail()
                'Attention! Created new ' . $prefix . ' id=' .    //но так как ее нет в LoggerInterface, то код ломается
                $body[$prefix . '_id'] . '!'                              //поэтому оставил так, чтобы код выводил сообщения в консоль
            );
        }
        return ConsumerInterface::MSG_ACK;
    }
}
