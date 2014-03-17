<?php

namespace Valera\Queue;

use PDOException;
use PDOStatement;
use Valera\Queueable;
use Valera\Queue;
use Valera\Queue\Exception\LogicException;
use Valera\Queue\Exception\RuntimeException;

/**
 * Relational database implementation of item queue
 *
 * @package Valera\Queue
 */
class Pdo implements Queue
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
    public function enqueue(Queueable $item)
    {
        $this->ensureItemRegistered($item);

        $query = <<<QUERY
INSERT INTO resource_queue (resource_hash)
SELECT * FROM (SELECT :resource_hash) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM resource_queue WHERE resource_hash = :resource_hash
)
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':resource_hash', $item->getHash());
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
            $item = unserialize($data);
            $this->removeFromCollection($item, 'resource_queue');
            $this->addToCollection($item, 'resource_in_progress');
            return $item;
        }

        return null;
    }

    /** @inheritDoc */
    public function resolveCompleted(Queueable $item)
    {
        $this->ensureInCollection($item, 'resource_in_progress');
        $this->removeFromCollection($item, 'resource_in_progress');
        $this->addToCollection($item, 'resource_completed');
    }

    /** @inheritDoc */
    public function resolveFailed(Queueable $item)
    {
        $this->ensureInCollection($item, 'resource_in_progress');
        $this->removeFromCollection($item, 'resource_in_progress');
        $this->addToCollection($item, 'resource_failed');
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

    protected function ensureItemRegistered(Queueable $item)
    {
        $query = <<<QUERY
INSERT INTO resource (hash, data)
SELECT * FROM (SELECT :hash, :data) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM resource WHERE hash = :hash
)
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':hash', $item->getHash());
        $stmt->bindValue(':data', serialize($item));
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

        $items = array();
        while ($stmt->fetch(\PDO::FETCH_BOUND)) {
            $items[] = unserialize($data);
        }

        return $items;
    }

    protected function addToCollection(Queueable $item, $table)
    {
        $query = <<<QUERY
INSERT INTO $table(resource_hash)
VALUES(:resource_hash)
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':resource_hash', $item->getHash());
        $stmt->execute();
    }

    protected function removeFromCollection(Queueable $item, $table)
    {
        $query = <<<QUERY
DELETE FROM $table
WHERE resource_hash = :resource_hash
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':resource_hash', $item->getHash());
        $stmt->execute();
    }

    protected function ensureInCollection(Queueable $item, $table)
    {
        $query = <<<QUERY
SELECT resource_hash
FROM $table
WHERE resource_hash = :resource_hash
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':resource_hash', $item->getHash());
        $stmt->execute();

        if (!$stmt->fetch()) {
            throw new LogicException('Item is not in progress');
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
