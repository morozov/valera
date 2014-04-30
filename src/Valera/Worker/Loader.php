<?php

namespace Valera\Worker;

use Valera\Loader\LoaderInterface;
use Valera\Loader\Result\Proxy as ResultProxy;
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

    protected function process()
    {
        $proxy = $this->getResultProxy();
        $this->loader->load($this->item, $proxy);

        return $proxy->getResult();
    }

    protected function getQueue()
    {
        return $this->sourceQueue;
    }

    protected function getResultProxy()
    {
        return new ResultProxy($this->contentQueue);
    }
}
