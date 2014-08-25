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
     * @param int|null $limit
     *
     * @return int The number of processed items
     */
    public function run($limit = null);
}
