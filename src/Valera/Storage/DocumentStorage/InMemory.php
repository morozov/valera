<?php

namespace Valera\Storage\DocumentStorage;

use DomainException;
use Valera\Resource;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

class InMemory implements DocumentStorage
{
    protected $documents = array();

    protected $index = array();

    public function create($id, array $data, array $resources)
    {
        if (isset($this->documents[$id])) {
            throw new DomainException('Document already exists');
        }

        $this->store($id, $data, $resources);
    }

    public function retrieve($id)
    {
        if (isset($this->documents[$id])) {
            return $this->documents[$id];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function findByResource(Resource $resource)
    {
        $documents = array();
        $hash = $resource->getHash();
        if (isset($this->index[$hash])) {
            foreach (array_keys($this->index[$hash]) as $id) {
                $documents[$id] = $this->retrieve($id);
            }
        }

        return new \ArrayIterator($documents);
    }

    public function update($id, array $data, array $resources)
    {
        if (isset($this->documents[$id])) {
            $this->store($id, $data, $resources);
        }
    }

    public function delete($id)
    {
        unset($this->documents[$id]);
        $this->removeFromIndex($id);
    }

    public function clean()
    {
        $this->documents = $this->index = array();
    }

    public function count()
    {
        return count($this->documents);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * @param string $id Document ID
     * @param array $data Document data
     * @param Resource[] $resources Embedded resources
     */
    protected function store($id, array $data, array $resources)
    {
        $this->documents[$id] = $data;

        $this->removeFromIndex($id);
        $this->addToIndex($id, $resources);
    }
    
    protected function addToIndex($id, $resources)
    {
        foreach ($resources as $resource) {
            $this->index[$resource->getHash()][$id] = true;
        }
    }

    /**
     * @param $id
     */
    protected function removeFromIndex($id)
    {
        foreach ($this->index as $hash => $ids) {
            if (isset($ids[$id])) {
                unset($this->index[$hash][$id]);
                if (!$this->index[$hash]) {
                    unset($this->index[$hash]);
                }
            }
        }
    }
}
