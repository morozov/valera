<?php

namespace Valera\Queue;

use Valera\Queueable;
use Valera\Resource;
use Valera\Queue;
use Valera\Queue\Exception\LogicException;

/**
 * MongoDB implementation of resource queue
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
     * Constructor
     */
    public function __construct(\MongoDB $db)
    {
        $this->db = $db;
        $this->setUp();
    }

    protected function setUp()
    {
        try {
            $this->db->counters->insert([
                '_id' => 'pending',
                'seq' => 0,
            ]);
        } catch (\MongoCursorException $e) {
            if ($e->getCode() !== 11000) {
                throw $e;
            }
        }

        $this->db->pending->ensureIndex(['sec' => 1]);
    }
    
    /** @inheritDoc */
    public function enqueue(Queueable $item)
    {
        try {
            /** @var \MongoCollection $pending */
            $this->db->pending->insert([
                '_id' => $item->getHash(),
                'seq' => $this->getNextSequence('pending'),
                'data' => $item->toArray(),
            ]);
        } catch (\MongoCursorException $e) {
            if ($e->getCode() !== 11000) {
                throw $e;
            }
        }
    }

    protected function getNextSequence($name)
    {
        $ret = $this->db->counters->findAndModify(
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
        $ret = $this->db->pending->findAndModify(
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

        $resource = Resource::fromArray($ret['data']);

        /** @var \MongoCollection $pending */
        $this->addToCollection($resource, 'in_progress');

        return $resource;
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
        $this->db->counters->drop();
        $this->db->pending->drop();
        $this->db->in_progress->drop();
        $this->db->completed->drop();
        $this->db->failed->drop();
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
        return $this->db->pending->count();
    }

    protected function getCollection($name)
    {
        $resources = array();
        $collection = $this->db->$name->find();
        foreach ($collection as $document) {
            $resources[] = Resource::fromArray($document['data']);
        }

        return $resources;
    }

    protected function addToCollection(Queueable $item, $name)
    {
        $this->db->$name->insert([
            '_id' => $item->getHash(),
            'data' => $item->toArray(),
        ]);
    }

    protected function ensureAndRemove(Queueable $item, $name)
    {
        $res = $this->db->$name->findAndModify(
            ['_id' => $item->getHash()],
            [],
            null,
            [
                'remove' => true,
            ]
        );
        
        if (!$res) {
            throw new LogicException('Resource is not in progress');
        }
    }
}
