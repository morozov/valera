<?php

namespace Valera\Storage\DocumentStorage;

use DomainException;
use Valera\Entity\Document;
use Valera\Resource;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

class InMemory implements DocumentStorage
{
    /**
     * @var \Valera\Entity\Document[]
     */
    protected $documents = array();

    protected $index = array();

    /**
     * {@inheritDoc}
     */
    public function create(Document $document)
    {
        $id = $document->getId();
        if (isset($this->documents[$id])) {
            throw new DomainException('Document already exists');
        }

        $this->store($id, $document, $document->getResources());
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function update(Document $document)
    {
        $id = $document->getId();
        if (isset($this->documents[$id])) {
            $this->store($id, $document, $document->getResources());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id)
    {
        unset($this->documents[$id]);
        $this->removeFromIndex($id);
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        $this->documents = $this->index = array();
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->documents);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * @param string $id Document ID
     * @param \Valera\Entity\Document $document Document
     * @param Resource[] $resources Embedded resources
     */
    protected function store($id, Document $document, array $resources)
    {
        $this->documents[$id] = $document;

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
