<?php

namespace Valera\Worker;

use Valera\DocumentIterator;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result as Result;
use Valera\Resource;
use Valera\Queue;
use Valera\Source;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

class Parser extends AbstractWorker
{
    /** @var Queue */
    protected $sourceQueue;

    /**
     * @var \Valera\Storage\DocumentStorage
     */
    protected $documentStorage;

    /**
     * @var \Valera\Storage\BlobStorage
     */
    protected $blobStorage;

    /**
     * @var \Valera\Parser\ParserInterface
     */
    protected $parser;

    /**
     * @var \Valera\DocumentIterator
     */
    protected $iterator;

    public function __construct(
        Queue $sourceQueue,
        Queue $contentQueue,
        DocumentStorage $documentStorage,
        BlobStorage $blobStorage,
        ParserInterface $parser,
        DocumentIterator $iterator
    ) {
        parent::__construct();

        $this->sourceQueue = $sourceQueue;
        $this->contentQueue = $contentQueue;
        $this->documentStorage = $documentStorage;
        $this->blobStorage = $blobStorage;
        $this->parser = $parser;
    }

    protected function getQueue()
    {
        return $this->contentQueue;
    }

    protected function createResult()
    {
        return new Result();
    }

    /**
     * @param \Valera\Content $content
     * @param \Valera\Parser\Result $result
     */
    protected function process($content, $result)
    {
        $documents = $this->parser->parse($content, $result);
        if ($documents !== null) {
            foreach ($documents as $id => $data) {
                $result->addDocument($id, $data);
            }
        }
    }

    /**
     * @param \Valera\Content $content
     * @param \Valera\Parser\Result $result
     */
    protected function handleSuccess($content, $result)
    {
        foreach ($result->getNewDocuments() as $id => $document) {
            $this->addDocument($id, $document);
        }

        foreach ($result->getUpdatedDocuments() as $id => $callback) {
            $this->updateDocument($id, $callback);
        }

        $resource = $content->getSource()->getResource();
        foreach ($result->getBlobs() as $contents) {
            $this->convertBlob($resource, $contents);
        }

        foreach ($result->getSources() as $source) {
            $this->addSource($source, $resource);
        }

        parent::handleSuccess($content, $result);
    }

    /**
     * @param \Valera\Blob\Remote[] $blobs
     */
    protected function enqueueBlobs(array $blobs)
    {
        foreach ($blobs as $blob) {
            $resource = $blob->getResource();
            $source = new Source(Resource::TYPE_BLOB, $resource);
            $this->sourceQueue->enqueue($source);
        }
    }

    /**
     * @param $id
     * @param array $document
     */
    protected function addDocument($id, array $document)
    {
        $blobs = $this->iterator->findEmbedded($document);
        $this->documentStorage->create($id, $document, $blobs);
        $this->enqueueBlobs($blobs);
    }

    /**
     * @param $id
     * @param callable $callback
     */
    protected function updateDocument($id, callable $callback)
    {
        $data = $this->documentStorage->retrieve($id);
        $data = $callback($data);
        $blobs = $this->iterator->findEmbedded($data);
        $this->documentStorage->update($id, $data, $blobs);
        $this->enqueueBlobs($blobs);
    }

    /**
     * @param \Valera\Resource $resource
     * @param $contents
     */
    protected function convertBlob(Resource $resource, $contents)
    {
        $path = $this->blobStorage->create($resource, $contents);
        $hash = $resource->getHash();
        $documents = $this->documentStorage->findByBlob($hash);
        foreach ($documents as $id => $document) {
            $this->iterator->convertEmbedded($document, $hash, $path);
            $blobs = $this->iterator->findEmbedded($document);
            $this->documentStorage->update($id, $document, $blobs);
        }
    }

    /**
     * @param array $params
     * @param \Valera\Resource $referrer
     */
    protected function addSource(array $params, Resource $referrer)
    {
        $resource = new Resource(
            $params['url'],
            $referrer,
            $params['method'],
            $params['headers'],
            $params['data']
        );
        $params = new Source($params['type'], $resource);
        $this->sourceQueue->enqueue($params);
    }
}
