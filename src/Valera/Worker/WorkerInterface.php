<?php

namespace Valera\Worker;

/**
 * Internal implementation of the worker
 */
interface WorkerInterface
{
    /**
     * @param \Traversable $tasks
     * @param callable     $resolver
     *
     * @return int Number of processed tasks
     */
    public function process(\Traversable $tasks, callable $resolver);
}
