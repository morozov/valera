<?php

namespace Valera\Queue;

use Valera\Queue;
use Valera\Queueable;

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
     * @var \Closure
     */
    private $converter;

    /**
     * @var \Valera\Queue\Resolver[]
     */
    private $observers;

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
     * @param \Closure|null $converter
     */
    public function __construct(Queue $queue, \Closure $converter = null)
    {
        $this->queue = $queue;
        if ($converter) {
            $this->converter = $converter;
        } else {
            $this->converter = function (Queueable $item) {
                return $item;
            };
        }
        $this->observers = new \SplObjectStorage();
    }

    /**
     * Returns the current element
     *
     * @return \Valera\Queueable
     */
    public function current()
    {
        if (!$this->current) {
            $item = $this->queue->dequeue();
            if ($item) {
                $value = $this->convert($item);
                $this->current = $value;
                $this->notify($value, $item);
            }
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

    public function attach(Resolver $observer)
    {
        $this->observers->attach($observer);
    }

    public function detach(Resolver $observer)
    {
        $this->observers->detach($observer);
    }

    public function notify($value, $item)
    {
        foreach ($this->observers as $observer) {
            $observer->update($value, $item);
        }
    }

    /**
     * Converts item into an object understandable by worker
     *
     * @param \Valera\Queueable $item
     * @return object
     */
    protected function convert(Queueable $item)
    {
        $converter = $this->converter;
        $item = $converter($item);

        return $item;
    }
}
