<?php

namespace Valera\Loader\Result;

use Valera\Content;
use Valera\Queue;
use Valera\Source;
use Valera\Result\Success as BaseSuccess;

class Success extends BaseSuccess
{
    /**
     * @var \Valera\Queue
     */
    protected $contentQueue;

    public function __construct(Queue $contentQueue)
    {
        $this->contentQueue = $contentQueue;
    }

    public function addContent($content, Source $source)
    {
        $content = new Content($content, $source);
        $this->contentQueue->enqueue($content);
    }
}
