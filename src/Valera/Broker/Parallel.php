<?php

namespace Valera\Broker;

use Psr\Log\LoggerInterface;
use Valera\Queue;
use Valera\Queue\Resolver;
use Valera\Worker\ParallelInterface;

/**
 * Parallel broker implementation
 */
class Parallel extends Base
{
    /**
     * @var \Valera\Worker\ParallelInterface
     */
    protected $worker;

    /**
     * Constructor
     *
     * @param \Valera\Queue                    $queue          Source queue
     * @param callable|null                    $converter      Queued item converter
     * @param \Valera\Worker\ParallelInterface $worker         Worker instance
     * @param \Valera\Worker\ResultHandler[]   $resultHandlers Result prototype
     * @param \Valera\Queue\Resolver           $resolver
     * @param \Psr\Log\LoggerInterface         $logger         Logger
     */
    public function __construct(
        Queue $queue,
        callable $converter = null,
        ParallelInterface $worker,
        array $resultHandlers,
        Resolver $resolver,
        LoggerInterface $logger
    ) {
        parent::__construct($queue, $converter, $resultHandlers, $resolver, $logger);
        $this->worker = $worker;
    }

    /** {@inheritDoc} */
    public function runIterator(\Iterator $values)
    {
        $this->worker->processMulti($values, function (\Closure $callback) {
            return function ($value) use ($callback) {
                $this->resolver->getItemAndResult($value, $item, $result);
                $callback($value, $item, $result);
                $this->handle($value, $item, $result);
            };
        });
    }
}
