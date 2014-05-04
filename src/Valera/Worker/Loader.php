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

    protected function process($source, $result)
    {
        $this->loader->load($source, $result);
    }

    protected function handleSuccess($source, $result)
    {
        /** @var \Valera\Loader\Result $result */
        $content = new Content(
            $result->getContent(),
            $source
        );

        $this->contentQueue->enqueue($content);
        parent::handleSuccess($source, $result);
    }
}
