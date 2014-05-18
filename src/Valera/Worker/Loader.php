<?php

namespace Valera\Worker;

use Psr\Log\LoggerInterface;
use Valera\Content;
use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Queue;

/**
 * Loader worker. Takes items from source queue and enqueues the downloaded contents
 * to content queue.
 */
class Loader extends AbstractWorker
{
    /**
     * Source queue
     *
     * @var \Valera\Queue
     */
    protected $sourceQueue;

    /**
     * Content queue
     *
     * @var \Valera\Queue
     */
    protected $contentQueue;

    /**
     * Loader implementation
     *
     * @var \Valera\Loader\LoaderInterface
     */
    protected $loader;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface       $logger
     * @param \Valera\Queue                  $sourceQueue
     * @param \Valera\Queue                  $contentQueue
     * @param \Valera\Loader\LoaderInterface $loader
     */
    public function __construct(
        LoggerInterface $logger,
        Queue $sourceQueue,
        Queue $contentQueue,
        LoaderInterface $loader
    ) {
        parent::__construct($sourceQueue, $logger);

        $this->sourceQueue = $sourceQueue;
        $this->contentQueue = $contentQueue;
        $this->loader = $loader;
    }

    protected function createResult()
    {
        return new Result();
    }

    /**
     * Processes source and resolves the result accordingly
     *
     * @param \Valera\Source $source
     * @param \Valera\Loader\Result $result
     */
    protected function process($source, $result)
    {
        $resource = $source->getResource();
        $this->logger->info('Downloading ' . $resource->getUrl());
        $this->loader->load($resource, $result);
    }

    /**
     * Handles successful download of the source
     *
     * @param \Valera\Source        $source
     * @param \Valera\Loader\Result $result
     */
    protected function handleSuccess($source, $result)
    {
        $content = $result->getContent();
        $mimeType = $result->getMimeType();

        $this->logger->debug(
            sprintf('Downloaded %d bytes (%s)', strlen($content), $mimeType)
        );

        /** @var \Valera\Loader\Result $result */
        $content = new Content($content, $mimeType, $source);

        $this->contentQueue->enqueue($content);
        parent::handleSuccess($source, $result);
    }
}
