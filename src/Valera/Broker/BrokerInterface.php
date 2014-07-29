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
     * @param int $maxItems
     * @return int The number of processed items
     */
    public function run($maxItems = null);
}
