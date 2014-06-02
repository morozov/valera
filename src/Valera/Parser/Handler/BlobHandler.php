<?php

namespace Valera\Parser\Handler;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Blob;
use Valera\Resource;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;
use Valera\Worker\ResultHandler;

/**
 * Handles blobs extracted by parser from content
 */
class BlobHandler implements ResultHandler
{
    use LoggerAwareTrait;

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
     * @param \Valera\Storage\BlobStorage     $blobStorage
     * @param \Valera\Storage\DocumentStorage $documentStorage
     * @param \Psr\Log\LoggerInterface        $logger
     */
    public function __construct(
        BlobStorage $blobStorage,
        DocumentStorage $documentStorage,
        LoggerInterface $logger
    ) {
        $this->blobStorage = $blobStorage;
        $this->documentStorage = $documentStorage;
        $this->setLogger($logger);
    }

    /**
     * Handles content parsing result
     *
     * @param \Valera\Content       $content
     * @param \Valera\Parser\Result $result
     */
    public function handle($content, $result)
    {
        /** @var \Valera\Parser\Result $result */
        foreach ($result->getBlobs() as $blob) {
            list($resource, $contents) = $blob;
            $path = $this->blobStorage->create($resource, $contents);
            $this->replaceResource($resource, $path);
        }
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
            }
        }
    }
}
