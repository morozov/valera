<?php

namespace Valera\ResourceQueue;

use Valera\Resource;
use Valera\ResourceQueue;

/**
 * Relational database implementation of resource queue
 *
 * @package Valera\ResourceQueue
 */
class Db implements ResourceQueue
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Creates the necessary database schema
     */
    public function setUp()
    {
    }

    /** @inheritDoc */
    public function enqueue(Resource $resource)
    {
    }

    /** @inheritDoc */
    public function dequeue()
    {
    }

    /** @inheritDoc */
    public function resolveSuccessful(Resource $resource)
    {
    }

    /** @inheritDoc */
    public function resolveFailed(Resource $resource)
    {
    }

    /** @inheritDoc */
    public function getInProgress()
    {
        return new \ArrayIterator(array());
    }

    /** @inheritDoc */
    public function getSuccessful()
    {
        return new \ArrayIterator(array());
    }

    /** @inheritDoc */
    public function getFailed()
    {
        return new \ArrayIterator(array());
    }

    /** @inheritDoc */
    public function count()
    {
    }
}
