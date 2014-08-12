<?php

namespace Valera\Parser\Handler;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Blob;
use Valera\Entity\Document;
use Valera\Queue\Writable;
use Valera\Source\BlobSource;
use Valera\Storage\DocumentStorage;
use Valera\Storage\BlobStorage;
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
     * @var \Valera\Storage\BlobStorage
     */
    protected $blobStorage;

    /**
     * @var \Valera\Queue\Writable
     */
    protected $sources;

    /**
     * @var \Valera\Parser\PostProcessor[]
     */
    protected $postProcessors = array();

    /**
     * Constructor
     *
     * @param \Valera\Storage\DocumentStorage $documentStorage
     * @param \Valera\Storage\BlobStorage     $blobStorage
     * @param \Valera\Queue\Writable              $sources
     * @param \Valera\Parser\PostProcessor[]  $postProcessors
     * @param \Psr\Log\LoggerInterface        $logger
     */
    public function __construct(
        DocumentStorage $documentStorage,
        BlobStorage $blobStorage,
        Writable $sources,
        array $postProcessors,
        LoggerInterface $logger
    ) {
        $this->documentStorage = $documentStorage;
        $this->blobStorage = $blobStorage;
        $this->sources = $sources;
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
        $this->postProcess($document, $referrer);
        $this->documentStorage->create($document);
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
            $this->documentStorage->update($document);
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
        foreach ($this->postProcessors as $postProcessor) {
            $postProcessor->process($document);
        }

        $document->replaceReference($referrer);
        $this->processResources($document);
    }

    /**
     * Enqueues embedded resources of the document for further processing
     *
     * @param \Valera\Entity\Document $document Document
     */
    protected function processResources(Document $document)
    {
        $resources = $document->getResources();
        foreach ($resources as $resource) {
            if ($this->blobStorage->isStored($resource)) {
                $path = $this->blobStorage->getPath($resource);
                $blob = new Blob($path, $resource);
                $document->replaceResource($blob);
            } else {
                $source = new BlobSource($resource);
                $this->sources->enqueue($source);
            }
        }
    }
}
