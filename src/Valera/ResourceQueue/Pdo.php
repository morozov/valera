<?php

namespace Valera\ResourceQueue;

use PDOException;
use PDOStatement;
use Valera\Resource;
use Valera\ResourceQueue;
use Valera\ResourceQueue\Exception\LogicException;
use Valera\ResourceQueue\Exception\RuntimeException;

/**
 * Relational database implementation of resource queue
 *
 * @package Valera\ResourceQueue
 */
class Pdo implements ResourceQueue
{
    /**
     * @var \PDO
     */
    protected $conn;

    /**
     * Constructor
     */
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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
        $this->ensureResourceRegistered($resource);

        $query = <<<QUERY
INSERT INTO resource_queue (resource_hash)
SELECT * FROM (SELECT :resource_hash) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM resource_queue WHERE resource_hash = :resource_hash
)
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':resource_hash', $resource->getHash());
        $stmt->execute();
    }

    /** @inheritDoc */
    public function dequeue()
    {
        $query = <<<QUERY
SELECT data
FROM resource_queue q
JOIN resource r
ON r.hash = q.resource_hash
ORDER BY position
LIMIT 1
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindColumn(1, $data);
        $stmt->execute();

        if ($stmt->fetch(\PDO::FETCH_BOUND)) {
            $resource = unserialize($data);
            $this->removeFromCollection($resource, 'resource_queue');
            $this->addToCollection($resource, 'resource_in_progress');
            return $resource;
        }

        return null;
    }

    /** @inheritDoc */
    public function resolveCompleted(Resource $resource)
    {
        $this->ensureInCollection($resource, 'resource_in_progress');
        $this->removeFromCollection($resource, 'resource_in_progress');
        $this->addToCollection($resource, 'resource_completed');
    }

    /** @inheritDoc */
    public function resolveFailed(Resource $resource)
    {
        $this->ensureInCollection($resource, 'resource_in_progress');
        $this->removeFromCollection($resource, 'resource_in_progress');
        $this->addToCollection($resource, 'resource_failed');
    }

    /** {@inheritDoc} */
    public function clean()
    {
        $query = <<<QUERY
DELETE FROM resource
QUERY;
        $this->query($query);
    }

    /** @inheritDoc */
    public function getInProgress()
    {
        return $this->getCollection('resource_in_progress');
    }

    /** @inheritDoc */
    public function getCompleted()
    {
        return $this->getCollection('resource_completed');
    }

    /** @inheritDoc */
    public function getFailed()
    {
        return $this->getCollection('resource_failed');
    }

    /** @inheritDoc */
    public function count()
    {
        $query = <<<QUERY
SELECT COUNT(*)
FROM resource_queue
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindColumn(1, $count, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->fetch(\PDO::FETCH_BOUND);

        return $count;
    }

    protected function ensureResourceRegistered(Resource $resource)
    {
        $query = <<<QUERY
INSERT INTO resource (hash, data)
SELECT * FROM (SELECT :hash, :data) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM resource WHERE hash = :hash
)
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':hash', $resource->getHash());
        $stmt->bindValue(':data', serialize($resource));
        $stmt->execute();
    }

    protected function getCollection($table)
    {
        $query = <<<QUERY
SELECT data
FROM $table c
JOIN resource r
ON r.hash = c.resource_hash
QUERY;
        $stmt = $this->conn->prepare($query);
        $stmt->bindColumn(1, $data);
        $stmt->execute();

        $resources = array();
        while ($stmt->fetch(\PDO::FETCH_BOUND)) {
            $resources[] = unserialize($data);
        }

        return $resources;
    }

    protected function addToCollection(Resource $resource, $table)
    {
        $query = <<<QUERY
INSERT INTO $table(resource_hash)
VALUES(:resource_hash)
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':resource_hash', $resource->getHash());
        $stmt->execute();
    }

    protected function removeFromCollection(Resource $resource, $table)
    {
        $query = <<<QUERY
DELETE FROM $table
WHERE resource_hash = :resource_hash
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':resource_hash', $resource->getHash());
        $stmt->execute();
    }

    protected function ensureInCollection(Resource $resource, $table)
    {
        $query = <<<QUERY
SELECT resource_hash
FROM $table
WHERE resource_hash = :resource_hash
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':resource_hash', $resource->getHash());
        $stmt->execute();

        if (!$stmt->fetch()) {
            throw new LogicException('Resource is not in progress');
        }
    }

    protected function query($query)
    {
        try {
            return $this->conn->query($query);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Failed to execute SQL query',
                null,
                $e
            );
        }
    }

    protected function execute(PDOStatement $stmt)
    {
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Failed to execute SQL query',
                null,
                $e
            );
        }
    }
}
