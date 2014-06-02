<?php

namespace Valera\Worker;

use Psr\Log\LoggerAwareInterface;

/**
 * Internal implementation of the worker
 */
interface WorkerInterface extends LoggerAwareInterface
{
    /**
     * Processes single item from queue and resolves result accordingly
     *
     * @param \Valera\Queueable     $item
     * @param \Valera\Worker\Result $result
     */
    public function process($item, $result);
}
