<?php

namespace Valera\Worker;

use Valera\Parser\ParserInterface;
use Valera\Parser\Result\Proxy as Result;
use Valera\Queue;
use Valera\Storage\BlobStorage;
use Valera\Storage\DocumentStorage;

class Parser extends AbstractWorker
{
    /** @var Queue */
    protected $sourceQueue;
    protected $documentStorage;
    protected $blobStorage;
    protected $parser;

    public function __construct(
        Queue $sourceQueue,
        Queue $contentQueue,
        DocumentStorage $documentStorage,
        BlobStorage $blobStorage,
        ParserInterface $parser
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

    protected function process()
    {
        $proxy = $this->getResultProxy();
        $this->parser->parse($this->item, $proxy);

        return $proxy->getResult();
    }

    protected function getResultProxy()
    {
        return new Result(
            $this->documentStorage,
            $this->blobStorage,
            $this->sourceQueue
        );
    }
}
