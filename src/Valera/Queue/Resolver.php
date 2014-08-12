<?php

namespace Valera\Queue;

use Valera\Queue;
use Valera\Queueable;
use Valera\Worker\Result;

/**
 * Queue item resolver
 */
class Resolver implements Observer
{
    /**
     * @var \Valera\Queue
     */
    private $queue;

    /**
     * @var \Valera\Worker\Result
     */
    private $result;

    /**
     * @var \SplObjectStorage
     */
    private $storage;

    /**
     * Constructor
     *
     * @param \Valera\Queue $queue
     * @param \Valera\Worker\Result $result
     */
    public function __construct(Queue $queue, Result $result)
    {
        $this->queue = $queue;
        $this->result = $result;
        $this->storage = new \SplObjectStorage();

        register_shutdown_function(function () {
            $this->handleUnexpectedExit();
        });
    }

    /**
     * Receive update from iterator
     *
     * @param object $value
     * @param \Valera\Queueable $item
     */
    public function update($value, Queueable $item)
    {
        $this->attach($value, $item);
    }

    public function wrapCallback($callback)
    {
        return function ($value) use ($callback) {
            list($item, $result) = $this->getItemAndResult($value);
            $callback($value, $item, $result);
            $this->detach($value);
        };
    }

    /**
     * Marks given item processing as successful
     *
     * @param object $value
     * @param \Valera\Queueable $item
     * @param \Valera\Worker\Result $result
     */
    public function resolve($value, Queueable $item, $result)
    {
        if ($result->getStatus()) {
            $this->queue->resolveCompleted($item);
        } else {
            $reason = $result->getReason();
            $this->queue->resolveFailed($item, $reason);
        }

        $this->detach($value);
    }

    /**
     * Creates relation between dequeued value and corresponding queue item and its processing result
     *
     * @param object $value
     * @param \Valera\Queueable $item
     */
    protected function attach($value, Queueable $item)
    {
        if (!$this->storage->contains($value)) {
            $result = clone $this->result;
            $this->storage->attach($value, array($item, $result));
        }
    }

    /**
     * Destroys relation between dequeued item and its processing result
     *
     * @param object $value
     */
    protected function detach($value)
    {
        $this->storage->detach($value);
    }

    /**
     * Returns result corresponding to the given item
     *
     * @param object $value
     *
     * @return array
     * @throws \Exception
     */
    public function getItemAndResult($value)
    {
        if (!$this->storage->offsetExists($value)) {
            throw new \Exception();
        }

        return $this->storage->offsetGet($value);
    }

    protected function handleUnexpectedExit()
    {
        foreach ($this->storage as $value) {
            list($item, $result) = $this->storage->getInfo();
            $result->fail('Script unexpectedly terminated');
            $this->resolve($value, $item, $result);
        }
    }
}
