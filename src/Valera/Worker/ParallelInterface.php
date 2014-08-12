<?php

namespace Valera\Worker;

use Psr\Log\LoggerAwareInterface;

/**
 * Worker that processes multiple items at a time
 */
interface ParallelInterface extends LoggerAwareInterface
{
    /**
     * Processes multiple items from queue and calls corresponding callback
     *
     * @param \Iterator $items
     * @param \Closure  $wrapper
     */
    public function processMulti(\Iterator $items, \Closure $wrapper);
}
