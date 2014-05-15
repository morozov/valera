<?php

namespace Valera\Worker;

use Valera\Content;
use Valera\Loader\LoaderInterface;
use Valera\Loader\Result;
use Valera\Result as BaseResult;
use Valera\Queue;

class Loader extends AbstractWorker
{
    /** @var Queue */
    protected $sourceQueue;
    protected $contentQueue;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    public function __construct(
        Queue $sourceQueue,
        Queue $contentQueue,
        LoaderInterface $loader
    ) {
        parent::__construct();

        $this->sourceQueue = $sourceQueue;
        $this->contentQueue = $contentQueue;
        $this->loader = $loader;
    }

    protected function getQueue()
    {
        return $this->sourceQueue;
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
        $this->loader->load($resource, $result);
    }

    protected function handleSuccess($source, $result)
    {
        /** @var \Valera\Loader\Result $result */
        $content = new Content(
            $result->getContent(),
            $result->getMimeType(),
            $source
        );

        $this->contentQueue->enqueue($content);
        parent::handleSuccess($source, $result);
    }
}
