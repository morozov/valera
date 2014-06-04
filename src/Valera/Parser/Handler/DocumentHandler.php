<?php

namespace Valera\Parser\Handler;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Entity\Document;
use Valera\Queue;
use Valera\Source;
use Valera\Storage\DocumentStorage;
use Valera\Worker\ResultHandler;

/**
 * Handles document data extracted by parser from content
 */
class DocumentHandler implements ResultHandler
{
    use LoggerAwareTrait;

    /**
     * @var \Valera\Storage\DocumentStorage
     */
    protected $documentStorage;

    /**
     * @var \Valera\Queue
     */
    protected $sourceQueue;

    /**
     * @var \Valera\Parser\PostProcessor[]
     */
    protected $postProcessors = array();

    /**
     * Constructor
     *
     * @param \Valera\Storage\DocumentStorage $documentStorage
     * @param \Valera\Queue                   $sourceQueue
     * @param \Valera\Parser\PostProcessor[]  $postProcessors
     * @param \Psr\Log\LoggerInterface        $logger
     */
    public function __construct(
        DocumentStorage $documentStorage,
        Queue $sourceQueue,
        array $postProcessors,
        LoggerInterface $logger
    ) {
        $this->documentStorage = $documentStorage;
        $this->sourceQueue = $sourceQueue;
        $this->postProcessors = $postProcessors;
        $this->setLogger($logger);
    }

    /**
     * Handles content parsing result
     *
     * @param \Valera\Content $content
     * @param \Valera\Parser\Result $result
     */
    public function handle($content, $result)
    {
        foreach ($result->getNewDocuments() as $id => $document) {
            $this->createDocument((string) $id, $document);
        }

        foreach ($result->getUpdatedDocuments() as $id => $callback) {
            $this->updateDocument((string) $id, $callback);
        }
    }

    /**
     * Creates new document in storage
     *
     * @param string $id   Document ID
     * @param array  $data Document data
     */
    protected function createDocument($id, array $data)
    {
        $document = new Document($id, $data);
        $this->documentStorage->create($document);
        $this->postProcess($document);
    }

    /**
     * Updates document in storage
     *
     * @param string   $id       Document ID
     * @param callable $callback Callback to apply
     */
    protected function updateDocument($id, callable $callback)
    {
        $document = $this->documentStorage->retrieve($id);
        if ($document) {
            $document->update($callback);
            $this->postProcess($document);
        }
    }

    /**
     * Post-processes document data
     *
     * @param Document $document
     */
    protected function postProcess(Document $document)
    {
        foreach ($this->postProcessors as $postProcessor) {
            $postProcessor->process($document);
        }

        $this->enqueueResources($document->getResources());
    }

    /**
     * Enqueues embedded resources of the document for further processing
     *
     * @param \Valera\Resource[] $resources
     */
    protected function enqueueResources(array $resources)
    {
        foreach ($resources as $resource) {
            $source = new Source('blob', $resource);
            $this->sourceQueue->enqueue($source);
        }
    }
}
