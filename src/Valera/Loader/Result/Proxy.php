<?php

namespace Valera\Loader\Result;

use Valera\Queue;
use Valera\Result\Proxy as BaseProxy;

class Proxy extends BaseProxy
{
    /**
     * @var \Valera\Queue
     */
    protected $contentQueue;

    public function __construct(Queue $contentQueue)
    {
        $this->contentQueue = $contentQueue;
    }

    /**
     * @return \Valera\Loader\Result\Success
     */
    protected function getSuccess()
    {
        return new Success($this->contentQueue);
    }
}
