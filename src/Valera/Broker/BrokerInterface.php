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
     * @param \Iterator $items
     *
     * @return int The number of processed items
     */
    public function run(\Iterator $items);
}
