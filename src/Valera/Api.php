<?php

namespace Valera;

use Assert\Assertion;
use Valera\Broker\BrokerInterface;
use Valera\Queue;
use Valera\Storage\DocumentStorage;
use Valera\Storage\BlobStorage;
use Valera\Source\DocumentSource as Source;

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
     * @var array
     */
    protected $sources;

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
        array $sources,
        callable $bootstrap = null
    ) {
        Assertion::allIsArray($sources);

        $this->sourceQueue = $sourceQueue;
        $this->contentQueue = $contentQueue;
        $this->blobStorage = $blobStorage;
        $this->documentStorage = $documentStorage;
        $this->broker = $broker;
        $this->sources = $sources;
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

        $this->enqueueSources();
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

    protected function enqueueSources()
    {
        foreach ($this->sources as $spec) {
            list($type, $url) = $spec;
            $resource = new Resource($url);
            $source = new Source($type, $resource);
            $this->sourceQueue->enqueue($source);
        }
    }
}
