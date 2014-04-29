<?php

namespace Valera\Parser\Result;

use Valera\Blob\Remote as RemoteBlob;
use Valera\Blob\Local as LocalBlob;
use Valera\Queue;
use Valera\Resource;
use Valera\Source;
use Valera\Storage\DocumentStorage;
use Valera\Storage\BlobStorage;
use Valera\Result\Success as BaseSuccess;

class Success extends BaseSuccess
{
    /**
     * @var \Valera\Storage\DocumentStorage
     */
    protected $documentStorage;

    /**
     * @var \Valera\Storage\BlobStorage
     */
    protected $blobStorage;

    /**
     * @var \Valera\Queue
     */
    protected $sourceQueue;

    public function __construct(
        DocumentStorage $documentStorage,
        BlobStorage $blobStorage,
        Queue $sourceQueue
    ) {
        $this->documentStorage = $documentStorage;
        $this->blobStorage = $blobStorage;
        $this->sourceQueue = $sourceQueue;
    }

    public function addDocument($id, array $data)
    {
        $blobs = $this->findBlobs($data);
        $this->documentStorage->create($id, $data, $blobs);
        $this->enqueueBlobs($blobs);
    }

    public function updateDocument($id, callable $callback)
    {
        $data = $this->documentStorage->retrieve($id);
        $data = $callback($data);
        $blobs = $this->findBlobs($data);
        $this->documentStorage->update($id, $data, $blobs);
        $this->enqueueBlobs($blobs);
    }

    public function addBlob(Resource $resource, $data)
    {
        $path = $this->blobStorage->create($resource, $data);
        $hash = $resource->getHash();
        $documents = $this->documentStorage->findByBlob($hash);
        foreach ($documents as $id => $data) {
            $this->convertBlob($data, $hash, $path);
            $blobs = $this->findBlobs($data);
            $this->documentStorage->update($id, $data, $blobs);
        }
    }

    public function addSource(
        $type,
        $url,
        Resource $referrer,
        $method = Resource::METHOD_GET,
        array $headers = array(),
        array $data = array()
    ) {
        $resource = new Resource($url, $referrer, $method, $headers, $data);
        $source = new Source($type, $resource);
        $this->sourceQueue->enqueue($source);
    }

    /**
     * @param RemoteBlob[] $blobs
     */
    protected function enqueueBlobs(array $blobs)
    {
        foreach ($blobs as $blob) {
            $resource = $blob->getResource();
            $source = new Source(Resource::TYPE_BLOB, $resource);
            $this->sourceQueue->enqueue($source);
        }
    }

    protected function iterate(array &$data, callable $filter, callable $callback)
    {
        array_walk_recursive($data, function (&$value) use ($filter, $callback) {
            if ($filter($value)) {
                $callback($value);
            }
        });
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function findBlobs(array $data)
    {
        $blobs = array();
        $this->iterate($data, function ($value) {
            return $value instanceof RemoteBlob;
        }, function (RemoteBlob $value) use (&$blobs) {
            $blobs[] = $value->getHash();
        });

        return $blobs;
    }

    protected function convertBlob(array &$data, RemoteBlob $blob, $path)
    {
        $this->iterate($data, function ($value) use ($blob) {
            return $value instanceof RemoteBlob
                && $value->getHash() === $blob->getHash();
        }, function (RemoteBlob &$value) use ($path) {
            $value = new LocalBlob($path);
        });
    }
}
