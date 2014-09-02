<?php

namespace Valera;

use Valera\Broker\BrokerInterface;
use Valera\Queue\Iterator;
use Valera\Queue\Resolver;
use Valera\Worker\Converter;
use Valera\Worker\WorkerInterface;

/**
 * Default broker implementation
 */
class Broker implements BrokerInterface
{
    /**
     * @var \Valera\Worker\ResultHandler[]
     */
    protected $resultHandlers;

    /**
     * Queue
     *
     * @var \Valera\Queue
     */
    protected $queue;

    /**
     * Queue resolver
     *
     * @var \Valera\Queue\Resolver
     */
    protected $resolver;

    /**
     * Constructor
     *
     * @param \Valera\Queue                  $queue          Source queue
     * @param \Valera\Worker\WorkerInterface $worker         Worker instance
     * @param \Valera\Queue\Resolver         $resolver       Job queue resolver
     */
    public function __construct(
        Queue $queue,
        WorkerInterface $worker,
        Resolver $resolver
    ) {
        $this->queue = $queue;
        $this->worker = $worker;
        $this->resolver = $resolver;
    }

    /**
     * Run broker
     *
     * @param int|null $limit
     *
     * @return int The number of processed items
     */
    public function run($limit = null)
    {
        $iterator = $this->getIterator($limit);
        return $this->worker->process($iterator, function ($task, callable $callback) {
            $this->resolver->resolve($task, $callback);
        });
    }

    /**
     * Returns queue iterator
     *
     * @param int|null $limit
     *
     * @return \Iterator
     */
    protected function getIterator($limit = null)
    {
        if ($this->worker instanceof Converter) {
            $iterator = new Iterator($this->queue, $this->worker);
        } else {
            $iterator = new Iterator($this->queue);
        }

        $iterator->attach($this->resolver);

        if ($limit) {
            $iterator = new \LimitIterator($iterator, 0, $limit);
        }

        return $iterator;
    }
}
