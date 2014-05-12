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
     * @var string
     */
    protected $name;

    /**
     * Constructor
     *
     * @param \PDO $conn
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(\PDO $conn, $name)
    {
        if (!is_string($name) || $name === '' || !ctype_alnum($name)) {
            throw new \InvalidArgumentException(
                'Queue name must be a non-empty alpha-numeric string'
            );
        }

        $this->conn = $conn;
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->name = $name;
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
INSERT INTO {$this->name}_queue (hash)
SELECT * FROM (SELECT :hash) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM {$this->name}_queue WHERE hash = :hash
)
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':hash', $item->getHash());
        $stmt->execute();
    }

    /** @inheritDoc */
    public function dequeue()
    {
        $query = <<<QUERY
SELECT data
FROM {$this->name}_queue q
JOIN resource r
ON r.hash = q.hash
ORDER BY position
LIMIT 1
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindColumn(1, $data);
        $stmt->execute();

        if ($stmt->fetch(\PDO::FETCH_BOUND)) {
            $item = unserialize($data);
            $this->removeFromCollection($item, 'queue');
            $this->addToCollection($item, 'in_progress');
            return $item;
        }

        return null;
    }

    /** @inheritDoc */
    public function resolveCompleted(Queueable $item)
    {
        $this->ensureInCollection($item, 'in_progress');
        $this->removeFromCollection($item, 'in_progress');
        $this->addToCollection($item, 'completed');
    }

    /** @inheritDoc */
    public function resolveFailed(Queueable $item, $reason)
    {
        $this->ensureInCollection($item, 'in_progress');
        $this->removeFromCollection($item, 'in_progress');
        $this->addToCollection($item, 'failed', array(
            'reason' => $reason,
        ));
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
        return $this->getCollection('in_progress');
    }

    /** @inheritDoc */
    public function getCompleted()
    {
        return $this->getCollection('completed');
    }

    /** @inheritDoc */
    public function getFailed()
    {
        return $this->getCollection('failed');
    }

    /** @inheritDoc */
    public function count()
    {
        $query = <<<QUERY
SELECT COUNT(*)
FROM {$this->name}_queue
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

    protected function getCollection($collection)
    {
        $query = <<<QUERY
SELECT data
FROM {$this->name}_$collection c
JOIN resource r
ON r.hash = c.hash
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

    /**
     * Adds item to specified collection
     *
     * @param Queueable $item
     * @param string $name
     * @param array $metadata
     */
    protected function addToCollection(
        Queueable $item,
        $name,
        array $metadata = array()
    ) {
        $columns = array('hash');
        $values = array(':hash' => $item->getHash());
        foreach ($metadata as $column => $value) {
            $columns[] = '`' . $column . '`';
            $values[':' . $column] = $value;
        }

        $columns = implode(', ', $columns);
        $placeholders = implode(', ', array_keys($values));
        $query = <<<QUERY
INSERT INTO {$this->name}_$name({$columns})
VALUES({$placeholders})
QUERY;

        $stmt = $this->conn->prepare($query);
        foreach ($values as $placeholder => $value) {
            $stmt->bindValue($placeholder, $value);
        }

        $stmt->execute();
    }

    protected function removeFromCollection(Queueable $item, $collection)
    {
        $query = <<<QUERY
DELETE FROM {$this->name}_$collection
WHERE hash = :hash
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':hash', $item->getHash());
        $stmt->execute();
    }

    protected function ensureInCollection(Queueable $item, $collection)
    {
        $query = <<<QUERY
SELECT hash
FROM {$this->name}_$collection
WHERE hash = :hash
QUERY;

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':hash', $item->getHash());
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
