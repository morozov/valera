<?php

namespace Valera\Queue;

use Valera\Queue;

/**
 * Queue iterator
 */
class Iterator implements \Iterator
{
    /**
     * @var \Valera\Queue
     */
    private $queue;

    /**
     * @var \Valera\Queueable
     */
    private $current;

    /**
     * @var int
     */
    private $key = 0;

    /**
     * Constructor
     *
     * @param \Valera\Queue $queue
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Returns the current element
     *
     * @return \Valera\Queueable
     */
    public function current()
    {
        if (!$this->current) {
            $this->current = $this->queue->dequeue();
        }

        return $this->current;
    }

    /**
     * Moves forward to next element
     *
     * @return void
     */
    public function next()
    {
        $this->current = null;
    }

    /**
     * Returns the key of the current element
     *
     * @return mixed
     */
    public function key()
    {
        return $this->valid() ? $this->key : null;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->current || count($this->queue) > 0;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void
     * @throws \Exception
     */
    public function rewind()
    {
        if ($this->key > 0) {
            throw new \Exception('Queue iterator cannot be rewound');
        }
    }
}
