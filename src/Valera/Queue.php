<?php

namespace Valera;

use Countable;

/**
 * Interface Queue
 *
 * @package Valera
 */
interface Queue extends Countable
{
    /**
     * Enqueues item
     *
     * @param \Valera\Queueable $item 
     */
    public function enqueue(Queueable $item);

    /**
     * Dequeues item
     *
     * @return \Valera\Queueable
     */
    public function dequeue();

    /**
     * Marks given item processing as successful
     *
     * @param \Valera\Queueable $item
     */
    public function resolveCompleted(Queueable $item);

    /**
     * Marks given item processing as failed
     *
     * @param \Valera\Queueable $item
     */
    public function resolveFailed(Queueable $item);

    /**
     * Cleans the queue
     */
    public function clean();

    /**
     * Returns iterator over item in progress
     *
     * @return \Iterator
     */
    public function getInProgress();

    /**
     * Returns iterator over successfully processed items
     *
     * @return \Iterator
     */
    public function getCompleted();

    /**
     * Returns iterator over unsuccessfully processed items
     *
     * @return \Iterator
     */
    public function getFailed();
}
