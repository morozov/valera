<?php

namespace Valera;

use Assert\Assertion;
use Valera\Broker\BrokerInterface;
use Valera\Queue;
use Valera\Storage\DocumentStorage;
use Valera\Storage\BlobStorage;

class Api
{
    /**
     * @var Queue
     */
    protected $sourceQueue;

    /**
     * @var Queue
     */
    protected $contentQueue;

    /**
     * @var DocumentStorage
     */
    protected $documentStorage;

    /**
     * @var BlobStorage
     */
    protected $blobStorage;

    /**
     * @var BrokerInterface
     */
    protected $broker;

    /**
     * @var callable
     */
    protected $bootstrap;

    public function __construct(
        Queue $sourceQueue,
        Queue $contentQueue,
        DocumentStorage $documentStorage,
        BlobStorage $blobStorage,
        BrokerInterface $broker,
        callable $bootstrap = null
    ) {
        $this->sourceQueue = $sourceQueue;
        $this->contentQueue = $contentQueue;
        $this->blobStorage = $blobStorage;
        $this->documentStorage = $documentStorage;
        $this->broker = $broker;
        $this->bootstrap = $bootstrap;
    }

    public function restartParser($force)
    {
        Assertion::boolean($force);

        if ($force) {
            $this->documentStorage->clean();
        }
        $this->reEnqueue($this->contentQueue, $force);
    }

    public function restartLoader($force)
    {
        Assertion::boolean($force);

        if ($force) {
            $this->blobStorage->clean();
            $this->restartParser($force);
        }
        $this->reEnqueue($this->sourceQueue, $force);
    }

    public function run()
    {
        if ($this->bootstrap) {
            $bootstrap = $this->bootstrap;
            if (!$bootstrap()) {
                return 0;
            }
        }

        return $this->broker->run();
    }

    protected function reEnqueue(Queue $queue, $all)
    {
        if ($all) {
            $queue->reEnqueueAll();
        } else {
            $queue->reEnqueueFailed();
        }
    }
}
