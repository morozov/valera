<?php

namespace Valera\Storage\BlobStorage;

use DomainException;
use Valera\Resource;
use Valera\Storage\BlobStorage;

class InMemory implements BlobStorage
{
    protected $blobs = array();

    /**
     * {@inheritDoc}
     */
    public function create(Resource $resource, $contents)
    {
        $hash = $resource->getHash();
        if (isset($this->blobs[$hash])) {
            throw new DomainException('Blob already exists');
        }

        $this->blobs[$hash] = $contents;

        return $hash;
    }

    public function retrieve(Resource $resource)
    {
        $hash = $resource->getHash();
        if (isset($this->blobs[$hash])) {
            return $this->blobs[$hash];
        }

        return null;
    }

    public function delete(Resource $resource)
    {
        $hash = $resource->getHash();
        unset($this->blobs[$hash]);
    }

    public function clean()
    {
        $this->blobs = array();
    }

    public function count()
    {
        return count($this->blobs);
    }
}