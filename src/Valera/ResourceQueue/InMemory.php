<?php

namespace Valera\ResourceQueue;

use ArrayIterator;
use SplQueue;
use Valera\Resource;
use Valera\ResourceQueue;
use Valera\ResourceQueue\Exception\LogicException;

/**
 * In-memory implementation of resource queue
 *
 * @package Valera\ResourceQueue
 */
class InMemory implements ResourceQueue
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
     * Resources in progress
     *
     * @var array
     */
    protected $inProgress = array();

    /**
     * Successfully processed resources
     *
     * @var array
     */
    protected $completed = array();

    /**
     * Unsuccessfully processed resources
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
    public function enqueue(Resource $resource)
    {
        $hash = $resource->getHash();

        if (isset($this->index[$hash])
            || isset($this->inProgress[$hash])
            || isset($this->failed[$hash])
            || isset($this->completed[$hash])
        ) {
            return;
        }

        $this->index[$hash] = true;
        $this->queue->enqueue($resource);
    }

    /** @inheritDoc */
    public function dequeue()
    {
        if ($this->queue->isEmpty()) {
            return null;
        }

        $resource = $this->queue->dequeue();
        $hash = $resource->getHash();

        $this->inProgress[$hash] = $resource;

        return $resource;
    }

    /** @inheritDoc */
    public function resolveCompleted(Resource $resource)
    {
        $hash = $resource->getHash();
        $this->stopProgress($hash);
        $this->completed[$hash] = $resource;
    }

    /** @inheritDoc */
    public function resolveFailed(Resource $resource)
    {
        $hash = $resource->getHash();
        $this->stopProgress($hash);
        $this->failed[$hash] = $resource;
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
     * Marks resource with the given hash as not in progress.
     *
     * Throws exception in case if it's not currently in progress.
     *
     * @throws \Valera\ResourceQueue\Exception\LogicException
     */
    protected function stopProgress($hash)
    {
        if (!isset($this->inProgress[$hash])) {
            throw new LogicException('Resource is not in progress');
        }
        unset($this->inProgress[$hash]);
    }
}