<?php

namespace Valera\Queue;

use Valera\Queueable;

/**
 * Writable end of queue
 */
interface Writable
{
    /**
     * Enqueues item
     *
     * @param \Valera\Queueable $item
     */
    public function enqueue(Queueable $item);
}
