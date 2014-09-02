<?php

namespace Valera\Worker;

use Psr\Log\LoggerAwareInterface;

/**
 * Internal implementation of the worker
 */
interface WorkerInterface
{
    /**
     * Processes single item from queue and resolves result accordingly
     *
     * @param mixed                 $value
     * @param \Valera\Worker\Result $result
     */
    public function process($value, $result);
}
