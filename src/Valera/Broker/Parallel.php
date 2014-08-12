<?php

namespace Valera\Broker;

use Psr\Log\LoggerInterface;
use Valera\Queue\Resolver;
use Valera\Queueable;
use Valera\Worker\Result;
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
     * @param \Valera\Worker\ParallelInterface $worker         Worker instance
     * @param \Valera\Worker\ResultHandler[]   $resultHandlers Result prototype
     * @param \Valera\Queue\Resolver           $resolver
     * @param \Psr\Log\LoggerInterface         $logger         Logger
     */
    public function __construct(
        ParallelInterface $worker,
        array $resultHandlers,
        Resolver $resolver,
        LoggerInterface $logger
    ) {
        parent::__construct($resultHandlers, $resolver, $logger);
        $this->worker = $worker;
    }

    /** {@inheritDoc} */
    public function run(\Iterator $values)
    {
        $this->worker->processMulti($values, function (\Closure $callback) {
            return function ($value) use ($callback) {
                list($item, $result) = $this->resolver->getItemAndResult($value);
                $callback($value, $item, $result);
                $this->handle($item, $result);
            };
        });
    }
}
