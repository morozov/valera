<?php

namespace Valera\Queue;

use ArrayIterator;
use SplQueue;
use Valera\Queueable;
use Valera\Queue;
use Valera\Queue\Exception\LogicException;

/**
 * In-memory implementation of queue
 *
 * @package Valera\Queue
 */
class InMemory implements Queue
{
    /**
     * Underlying queue
     *
     * @var \SplQueue
     */
    protected $queue;

    /**
     * Underlying queue index
     *
     * @var array
     */
    protected $index;

    /**
     * Items in progress
     *
     * @var array
     */
    protected $inProgress = array();

    /**
     * Successfully processed items
     *
     * @var array
     */
    protected $completed = array();

    /**
     * Unsuccessfully processed items
     *
     * @var array
     */
    protected $failed = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    /** @inheritDoc */
    public function enqueue(Queueable $item)
    {
        $hash = $item->getHash();

        if (isset($this->index[$hash])
            || isset($this->inProgress[$hash])
            || isset($this->failed[$hash])
            || isset($this->completed[$hash])
        ) {
            return;
        }

        $this->index[$hash] = true;
        $this->queue->enqueue($item);
    }

    /** @inheritDoc */
    public function dequeue()
    {
        if ($this->queue->isEmpty()) {
            return null;
        }

        $item = $this->queue->dequeue();
        $hash = $item->getHash();

        $this->inProgress[$hash] = $item;

        return $item;
    }

    /** @inheritDoc */
    public function resolveCompleted(Queueable $item)
    {
        $hash = $item->getHash();
        $this->stopProgress($hash);
        $this->completed[$hash] = $item;
    }

    /** @inheritDoc */
    public function resolveFailed(Queueable $item)
    {
        $hash = $item->getHash();
        $this->stopProgress($hash);
        $this->failed[$hash] = $item;
    }

    /** {@inheritDoc} */
    public function clean()
    {
        while (!$this->queue->isEmpty()) {
            $this->queue->dequeue();
        }

        $this->index = $this->inProgress = $this->completed = $this->failed
            = array();
    }

    /** @inheritDoc */
    public function getInProgress()
    {
        return new ArrayIterator($this->inProgress);
    }

    /** @inheritDoc */
    public function getCompleted()
    {
        return new ArrayIterator($this->completed);
    }

    /** @inheritDoc */
    public function getFailed()
    {
        return new ArrayIterator($this->failed);
    }

    /**
     * Returns count of elements in queue
     *
     * @return int
     */
    public function count()
    {
        return count($this->queue);
    }

    /**
     * Marks item with the given hash as not in progress.
     *
     * Throws exception in case if it's not currently in progress.
     *
     * @throws \Valera\Queue\Exception\LogicException
     */
    protected function stopProgress($hash)
    {
        if (!isset($this->inProgress[$hash])) {
            throw new LogicException('Item is not in progress');
        }
        unset($this->inProgress[$hash]);
    }
}
