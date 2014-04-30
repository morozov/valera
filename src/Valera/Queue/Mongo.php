<?php

namespace Valera\Queue;

use MongoCursorException;
use Valera\Queueable;
use Valera\Queue;
use Valera\Queue\Exception\LogicException;

/**
 * MongoDB implementation of queue
 *
 * @package Valera\Queue
 */
class Mongo implements Queue
{
    /**
     * @var \MongoDB
     */
    protected $db;

    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor
     */
    public function __construct(\MongoDB $db, $name)
    {
        if (!is_string($name) || $name === '' || !ctype_alnum($name)) {
            throw new \InvalidArgumentException(
                'Queue name must be a non-empty alpha-numeric string'
            );
        }

        $this->db = $db;
        $this->name = $name;
        $this->setUp();
    }

    protected function setUp()
    {
        try {
            $this->db->{$this->name . '_counters'}->insert([
                '_id' => 'pending',
                'seq' => 0,
            ]);
        } catch (MongoCursorException $e) {
            if ($e->getCode() !== 11000) {
                throw $e;
            }
        }

        $this->db->{$this->name . '_pending'}->ensureIndex(['sec' => 1]);
    }
    
    /** @inheritDoc */
    public function enqueue(Queueable $item)
    {
        $d = [
            '_id' => $item->getHash(),
            'seq' => $this->getNextSequence('pending'),
            'data' => serialize($item),
        ];
        try {
            /** @var \MongoCollection $pending */
            $this->db->{$this->name . '_pending'}->insert($d);
        } catch (MongoCursorException $e) {
            if ($e->getCode() !== 11000) {
                throw $e;
            }
        }
    }

    protected function getNextSequence($name)
    {
        $ret = $this->db->{$this->name . '_counters'}->findAndModify(
            ['_id' => $name],
            ['$inc' => ['seq' => 1]],
            null,
            ['new' => true]
        );

        return $ret['seq'];
    }
    
    /** @inheritDoc */
    public function dequeue()
    {
        $ret = $this->db->{$this->name . '_pending'}->findAndModify(
            [],
            [],
            null,
            [
                'sort' => ['seq' => 1],
                'remove' => true,
            ]
        );

        if (!$ret) {
            return null;
        }

        $item = unserialize($ret['data']);

        /** @var \MongoCollection $pending */
        $this->addToCollection($item, 'in_progress');

        return $item;
    }

    /** @inheritDoc */
    public function resolveCompleted(Queueable $item)
    {
        $this->ensureAndRemove($item, 'in_progress');
        $this->addToCollection($item, 'completed');
    }

    /** @inheritDoc */
    public function resolveFailed(Queueable $item)
    {
        $this->ensureAndRemove($item, 'in_progress');
        $this->addToCollection($item, 'failed');
    }

    /** {@inheritDoc} */
    public function clean()
    {
        $this->db->{$this->name . '_counters'}->drop();
        $this->db->{$this->name . '_pending'}->drop();
        $this->db->{$this->name . '_in_progress'}->drop();
        $this->db->{$this->name . '_completed'}->drop();
        $this->db->{$this->name . '_failed'}->drop();
        $this->setUp();
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
        return $this->db->{$this->name . '_pending'}->count();
    }

    protected function getCollection($name)
    {
        $items = array();
        $collection = $this->db->{$this->name . '_' . $name}->find();
        foreach ($collection as $document) {
            $items[] = unserialize($document['data']);
        }

        return $items;
    }

    protected function addToCollection(Queueable $item, $name)
    {
        $this->db->{$this->name . '_' . $name}->insert([
            '_id' => $item->getHash(),
            'data' => serialize($item),
        ]);
    }

    protected function ensureAndRemove(Queueable $item, $name)
    {
        $res = $this->db->{$this->name . '_' . $name}->findAndModify(
            ['_id' => $item->getHash()],
            [],
            null,
            [
                'remove' => true,
            ]
        );
        
        if (!$res) {
            throw new LogicException('Item is not in progress');
        }
    }
}
