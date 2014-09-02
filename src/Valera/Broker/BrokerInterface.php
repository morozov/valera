<?php

namespace Valera\Broker;

/**
 * Broker interface
 */
interface BrokerInterface
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
