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
     * Pending items
     *
     * @var \SplQueue
     */
    protected $pending;

    /**
     * Pending items index
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
        $this->pending = new SplQueue();
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

        $this->addToPending($item);
    }

    protected function addToPending(Queueable $item)
    {
        $hash = $item->getHash();

        $this->index[$hash] = true;
        $this->pending->enqueue($item);
    }

    /** @inheritDoc */
    public function dequeue()
    {
        if ($this->pending->isEmpty()) {
            return null;
        }

        $item = $this->pending->dequeue();
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
    public function resolveFailed(Queueable $item, $reason)
    {
        $hash = $item->getHash();
        $this->stopProgress($hash);
        $this->failed[$hash] = $item;
    }

    /** {@inheritDoc} */
    public function clean()
    {
        while (!$this->pending->isEmpty()) {
            $this->pending->dequeue();
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
        return count($this->pending);
    }

    /**
     * Re-enqueues failed items
     */
    public function reEnqueueFailed()
    {
        foreach ($this->failed as $item) {
            $this->addToPending($item);
        }

        $this->failed = array();
    }

    /**
     * Re-enqueues failed and completed items
     */
    public function reEnqueueAll()
    {
        $this->reEnqueueFailed();

        foreach ($this->completed as $item) {
            $this->addToPending($item);
        }

        $this->completed = array();
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
