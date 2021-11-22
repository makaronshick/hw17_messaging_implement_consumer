<?php

declare(strict_types=1);

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * @author Vitaliy Bilotil <bilotilvv@gmail.com>
 */
class QohIsRunningOutNotifierConsumerCallback implements ConsumerInterface
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
        $body = json_decode($msg->getBody(), true);
        $values = $body['values'];

        if(array_key_exists('qoh', $values) && $body['values']['qoh'] < 5) {
            $this->logger->info(                                                   //по заданию нужно тут применить функцию mail()
                'Attention! The product with id=' . $body['product_id'] .  //но так как ее нет в LoggerInterface, то код ломается
                ' is out of stock!' . ' ' .                                        //поэтому оставил так, чтобы код выводил сообщения в консоль
                'Current balance ' . $body['values']['qoh'] . '!' . ' ' .
                'Previous value is ' . $body['previous_values']['qoh'] . '!'
            );
        }
        return ConsumerInterface::MSG_ACK;
    }
}
