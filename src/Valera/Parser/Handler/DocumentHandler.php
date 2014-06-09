<?php

namespace Valera\Parser\Handler;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Entity\Document;
use Valera\Queue;
use Valera\Source\BlobSource;
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
        $referrer = $content->getResource()->getUrl();

        foreach ($result->getNewDocuments() as $id => $document) {
            $this->createDocument((string) $id, $document, $referrer);
        }

        foreach ($result->getUpdatedDocuments() as $id => $callback) {
            $this->updateDocument((string) $id, $callback, $referrer);
        }
    }

    /**
     * Creates new document in storage
     *
     * @param string $id       Document ID
     * @param array  $data     Document data
     * @param string $referrer Referrer for related resources
     */
    protected function createDocument($id, array $data, $referrer)
    {
        $document = new Document($id, $data);
        $this->documentStorage->create($document);
        $this->postProcess($document, $referrer);
    }

    /**
     * Updates document in storage
     *
     * @param string   $id       Document ID
     * @param callable $callback Callback to apply
     * @param string   $referrer Referrer for related resources
     */
    protected function updateDocument($id, callable $callback, $referrer)
    {
        $document = $this->documentStorage->retrieve($id);
        if ($document) {
            $document->update($callback);
            $this->postProcess($document, $referrer);
        }
    }

    /**
     * Post-processes document data
     *
     * @param Document $document
     * @param string   $referrer Referrer for related resources
     */
    protected function postProcess(Document $document, $referrer)
    {
        $document->replaceReference($referrer);
        foreach ($this->postProcessors as $postProcessor) {
            $postProcessor->process($document);
        }

        $this->enqueueResources($document, $referrer);
    }

    /**
     * Enqueues embedded resources of the document for further processing
     *
     * @param \Valera\Entity\Document $document Document
     */
    protected function enqueueResources(Document $document)
    {
        $resources = $document->getResources();
        foreach ($resources as $resource) {
            $source = new BlobSource($resource);
            $this->sourceQueue->enqueue($source);
        }
    }
}
