<?php

namespace Valera\Loader\Handler;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Content;
use Valera\Queue;
use Valera\Worker\ResultHandler;

/**
 * Downloaded content handler. Enqueues the content for parsing.
 */
class ContentHandler implements ResultHandler
{
    use LoggerAwareTrait;

    /**
     * Content queue
     *
     * @var \Valera\Queue
     */
    protected $contentQueue;

    /**
     * Constructor
     *
     * @param \Valera\Queue            $contentQueue
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(Queue $contentQueue, LoggerInterface $logger)
    {
        $this->contentQueue = $contentQueue;
        $this->setLogger($logger);
    }

    /**
     * Handles successful result
     *
     * @param \Valera\Source        $source Source being downloaded
     * @param \Valera\Loader\Result $result Download result
     */
    public function handle($source, $result)
    {
        /** @var \Valera\Loader\Result $result */
        $content = $result->getContent();
        $mimeType = $result->getMimeType();

        $this->logger->debug(
            sprintf('Downloaded %d bytes (%s)', strlen($content), $mimeType)
        );

        /** @var \Valera\Source $source */
        $content = new Content($content, $mimeType, $source);

        $this->contentQueue->enqueue($content);
    }
}
