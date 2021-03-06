<?php

namespace Valera\Loader\Handler;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Blob;
use Valera\Content;
use Valera\Queue\Writable;
use Valera\Resource;
use Valera\Source\BlobSource;
use Valera\Source\DocumentSource;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;
use Valera\Worker\ResultHandler;

/**
 * Handles downloaded contents
 */
class ContentHandler implements ResultHandler
{
    use LoggerAwareTrait;

    /**
     * Content queue
     *
     * @var \Valera\Queue\Writable
     */
    protected $contents;

    /**
     * @var \Valera\Storage\BlobStorage
     */
    protected $blobStorage;

    /**
     * @var \Valera\Storage\DocumentStorage
     */
    protected $documentStorage;

    /**
     * Constructor
     *
     * @param \Valera\Queue\Writable              $contents
     * @param \Valera\Storage\BlobStorage     $blobStorage
     * @param \Valera\Storage\DocumentStorage $documentStorage
     * @param \Psr\Log\LoggerInterface        $logger
     */
    public function __construct(
        Writable $contents,
        BlobStorage $blobStorage,
        DocumentStorage $documentStorage,
        LoggerInterface $logger
    ) {
        $this->contents = $contents;
        $this->blobStorage = $blobStorage;
        $this->documentStorage = $documentStorage;
        $this->setLogger($logger);
    }

    /**
     * Handles successful result
     *
     * @param \Valera\Source        $source Source being downloaded
     * @param \Valera\Loader\Result $result Download result
     */
    public function handle($source, $result)
    {
        /** @var \Valera\Loader\Result $result */
        $contents = $result->getContent();
        $mimeType = $result->getMimeType();

        $this->logger->debug(
            sprintf('Downloaded %d bytes (%s)', strlen($contents), $mimeType)
        );

        if ($source instanceof DocumentSource) {
            $this->handleDocumentContents($source, $contents, $mimeType);
        } elseif ($source instanceof BlobSource) {
            $this->handleBlobContents($source, $contents);
        }
    }

    /**
     * Handles successful result
     *
     * @param \Valera\Source\DocumentSource $source Source being downloaded
     * @param string                        $content
     * @param string                        $mimeType
     */
    protected function handleDocumentContents(DocumentSource $source, $content, $mimeType)
    {
        /** @var \Valera\Source $source */
        $content = new Content($content, $mimeType, $source);

        $this->contents->enqueue($content);
    }

    /**
     * Handles successful result
     *
     * @param \Valera\Source\BlobSource $source Source being downloaded
     * @param string                    $contents
     */
    public function handleBlobContents(BlobSource $source, $contents)
    {
        $resource = $source->getResource();
        $path = $this->blobStorage->create($resource, $contents);
        $this->replaceResource($resource, $path);
    }

    /**
     * Replaces the given resource with corresponding blob in all document that have this resource embedded
     *
     * @param \Valera\Resource $resource Origin resource
     * @param string           $path     Blob path
     */
    protected function replaceResource(Resource $resource, $path)
    {
        $documents = $this->documentStorage->findByResource($resource);
        if ($documents) {
            $blob = new Blob($path, $resource);
            foreach ($documents as $document) {
                $document->replaceResource($blob);
                $this->documentStorage->update($document);
            }
        }
    }
}
