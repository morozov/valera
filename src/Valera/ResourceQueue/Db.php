<?php

namespace Valera\ResourceQueue;

use Doctrine\DBAL\Connection;
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
     * @var \Doctrine\DBAL\Connection
     */
    protected $conn;

    /**
     * Constructor
     */
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
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
        $order = $this->conn->quoteIdentifier('order');
        $query = <<<QUERY
INSERT INTO
  resource_queue (resource_id, $order)
SELECT :resource_id, IFNULL(MAX($order), 0) + 1 FROM resource_queue;
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($resource->getHash());
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
