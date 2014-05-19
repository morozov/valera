<?php

namespace Valera\Worker;

use Psr\Log\LoggerAwareInterface;

/**
 * Worker interface
 */
interface WorkerInterface extends LoggerAwareInterface
{
    /**
     * Run the worker
     *
     * @return int The number of processed items
     */
    public function run();
}
