<?php

namespace Valera\Worker;

use Valera\Loader\LoaderInterface;
use Valera\Queue;

class Loader
{
    /** @var Queue */
    protected $resourceQueue;
    protected $contentQueue;
    protected $loader;

    public function __construct($resourceQueue, $contentQueue, LoaderInterface$loader)
    {
        $this->resourceQueue = $resourceQueue;
        $this->contentQueue = $contentQueue;
    }

    public function run()
    {
        while (count($this->resourceQueue) > 0) {
            $resource = $this->resourceQueue->dequeue();
        }
    }
}
