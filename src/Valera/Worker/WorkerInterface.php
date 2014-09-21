<?php

namespace Valera\Worker;
use Valera\Queue\Resolver;

/**
 * Internal implementation of the worker
 */
interface WorkerInterface
{
    /**
     * @param \Traversable           $tasks
     * @param \Valera\Queue\Resolver $resolver
     *
     * @return int Number of processed tasks
     */
    public function process(\Traversable $tasks, Resolver $resolver);
}
