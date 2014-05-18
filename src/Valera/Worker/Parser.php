<?php

namespace Valera\Worker;

use Psr\Log\LoggerInterface;
use Valera\Blob;
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
        LoggerInterface $logger,
        Queue $sourceQueue,
        Queue $contentQueue,
        DocumentStorage $documentStorage,
        BlobStorage $blobStorage,
        ParserInterface $parser,
        DocumentIterator $iterator
    ) {
        parent::__construct($contentQueue, $logger);

        $this->sourceQueue = $sourceQueue;
        $this->contentQueue = $contentQueue;
        $this->documentStorage = $documentStorage;
        $this->blobStorage = $blobStorage;
        $this->parser = $parser;
        $this->iterator = $iterator;
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

        foreach ($result->getBlobs() as $blob) {
            list($resource, $contents) = $blob;
            $this->convertResource($resource, $contents);
        }

        $referrer = $content->getSource()->getResource()->getUrl();
        foreach ($result->getSources() as $source) {
            $this->addSource(array_merge($source, array(
                'referrer' => $referrer,
            )));
        }

        parent::handleSuccess($content, $result);
    }

    /**
     * @param \Valera\Resource[] $resources
     */
    protected function enqueueResources(array $resources)
    {
        foreach ($resources as $resource) {
            $source = new Source('blob', $resource);
            $this->sourceQueue->enqueue($source);
        }
    }

    /**
     * @param $id
     * @param array $document
     */
    protected function addDocument($id, array $document)
    {
        $resources = $this->findResources($document);
        $this->documentStorage->create($id, $document, $resources);
        $this->enqueueResources($resources);
    }

    /**
     * @param $id
     * @param callable $callback
     */
    protected function updateDocument($id, callable $callback)
    {
        $data = $this->documentStorage->retrieve($id);
        $data = $callback($data);
        $resources = $this->findResources($data);
        $this->documentStorage->update($id, $data, $resources);
        $this->enqueueResources($resources);
    }

    /**
     * @param array $document
     *
     * @return array
     */
    protected function findResources(array $document)
    {
        $resources = array();
        $this->iterator->iterate($document, function ($value) {
            return $value instanceof Resource;
        }, function (Resource $value) use (&$resources) {
            $resources[] = $value;
        });

        return $resources;
    }

    /**
     * @param \Valera\Resource $resource
     * @param string $contents
     */
    protected function convertResource(Resource $resource, $contents)
    {
        $path = $this->blobStorage->create($resource, $contents);
        $documents = $this->documentStorage->findByResource($resource);
        foreach ($documents as $id => $document) {
            $this->iterator->iterate($document, function ($value) use ($resource) {
                return $value instanceof Resource
                && $value->getHash() === $resource->getHash();
            }, function (Resource &$value) use ($resource, $path) {
                $value = new Blob($path, $resource);
            });
            $resources = $this->findResources($document);
            $this->documentStorage->update($id, $document, $resources);
        }
    }

    /**
     * @param array $params
     */
    protected function addSource(array $params)
    {
        $resource = new Resource(
            $params['url'],
            $params['referrer'],
            $params['method'],
            $params['headers'],
            $params['data']
        );

        $source = new Source($params['type'], $resource);
        $this->sourceQueue->enqueue($source);
    }
}
