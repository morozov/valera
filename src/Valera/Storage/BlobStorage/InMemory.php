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
        if ($this->isStored($resource)) {
            throw new DomainException('Blob already exists');
        }

        $path = $resource->getHash();
        $this->blobs[$path] = $contents;

        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function isStored(Resource $resource)
    {
        $path = $resource->getHash();
        return isset($this->blobs[$path]);
    }

    /**
     * {@inheritDoc}
     */
    public function getPath(Resource $resource)
    {
        return $resource->getHash();
    }

    public function retrieve(Resource $resource)
    {
        if ($this->isStored($resource)) {
            $path = $this->getPath($resource);
            return $this->blobs[$path];
        }

        return null;
    }

    public function delete(Resource $resource)
    {
        $path = $this->getPath($resource);
        unset($this->blobs[$path]);
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
