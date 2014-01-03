<?php

namespace Valera;

use Countable;

/**
 * Interface ResourceQueue
 *
 * @package Valera
 */
interface ResourceQueue extends Countable
{
    /**
     * Enqueues resource
     *
     * @param \Valera\Resource $resource 
     */
    public function enqueue(Resource $resource);

    /**
     * Dequeues resource
     *
     * @return \Valera\Resource
     */
    public function dequeue();

    /**
     * Marks given resource processing as successful
     *
     * @param \Valera\Resource $resource
     */
    public function resolveCompleted(Resource $resource);

    /**
     * Marks given resource processing as failed
     *
     * @param \Valera\Resource $resource
     */
    public function resolveFailed(Resource $resource);

    /**
     * Cleans the queue
     */
    public function clean();

    /**
     * Returns iterator over resources in progress
     *
     * @return \Iterator
     */
    public function getInProgress();

    /**
     * Returns iterator over successfully processed resources
     *
     * @return \Iterator
     */
    public function getCompleted();

    /**
     * Returns iterator over unsuccessfully processed resources
     *
     * @return \Iterator
     */
    public function getFailed();
}
