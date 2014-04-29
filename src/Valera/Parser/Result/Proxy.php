<?php

namespace Valera\Parser\Result;

use Valera\Queue;
use Valera\Result\Proxy as BaseProxy;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

/**
 * @method Success resolve()
 */
class Proxy extends BaseProxy
{
    public function __construct(
        DocumentStorage $documentStorage,
        BlobStorage $blobStorage,
        Queue $sourceQueue
    ) {
        $this->documentStorage = $documentStorage;
        $this->blobStorage = $blobStorage;
        $this->sourceQueue = $sourceQueue;
    }

    /**
     * @return Success
     */
    protected function getSuccess()
    {
        return new Success(
            $this->documentStorage,
            $this->blobStorage,
            $this->sourceQueue
        );
    }
}
