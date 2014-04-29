<?php

namespace Valera\Storage\DocumentStorage;

use DomainException;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

class InMemory implements DocumentStorage
{
    protected $documents = array();

    protected $index = array();

    public function create($id, array $data, array $blobs)
    {
        if (isset($this->documents[$id])) {
            throw new DomainException('Document already exists');
        }

        $this->store($id, $data, $blobs);
    }

    public function retrieve($id)
    {
        if (isset($this->documents[$id])) {
            return $this->documents[$id]['data'];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function findByBlob($hash)
    {
        if (!isset($this->index[$hash])) {
            return array();
        }

        $documents = array();
        foreach (array_keys($this->index[$hash]) as $id) {
            $documents[$id] = $this->retrieve($id);
        }

        return $documents;
    }

    public function update($id, array $data, array $blobs)
    {
        if (isset($this->documents[$id])) {
            $this->store($id, $data, $blobs);
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

    /**
     * @param string $id Document ID
     * @param array $data Document data
     * @param array $blobs Related blobs
     */
    protected function store($id, array $data, array $blobs)
    {
        $this->documents[$id] = array(
            'data' => $data,
            'blobs' => $blobs,
        );

        $this->removeFromIndex($id);
        $this->addToIndex($id, $blobs);
    }
    
    protected function addToIndex($id, $blobs)
    {
        foreach ($blobs as $hash) {
            $this->index[$hash][$id] = true;
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
