<?php

namespace Valera\Broker;

use Psr\Log\LoggerAwareInterface;

/**
 * Broker interface
 */
interface BrokerInterface extends LoggerAwareInterface
{
    /**
     * Run broker
     *
     * @return int The number of processed items
     */
    public function run();
}
