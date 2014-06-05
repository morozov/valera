<?php

namespace Valera\Parser\Handler;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Queue;
use Valera\Resource;
use Valera\Source\DocumentSource;
use Valera\Worker\ResultHandler;

/**
 * Handles new sources extracted by parser from content
 */
class SourceHandler implements ResultHandler
{
    use LoggerAwareTrait;

    /**
     * @var \Valera\Queue
     */
    protected $sourceQueue;

    /**
     * Constructor
     *
     * @param \Valera\Queue            $sourceQueue
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Queue $sourceQueue,
        LoggerInterface $logger
    ) {
        $this->sourceQueue = $sourceQueue;
        $this->setLogger($logger);
    }

    /**
     * Handles content parsing result
     *
     * @param \Valera\Content $content
     * @param \Valera\Parser\Result $result
     */
    public function handle($content, $result)
    {
        /** @var \Valera\Content $content */
        $referrer = $content->getSource()->getResource()->getUrl();

        /** @var \Valera\Parser\Result $result */
        foreach ($result->getSources() as $source) {
            $this->enqueueSource(array_merge($source, array(
                'referrer' => $referrer,
            )));
        }
    }

    /**
     * Enqueues newly discovered for further processing
     *
     * @param array $params
     */
    protected function enqueueSource(array $params)
    {
        $resource = new Resource(
            $params['url'],
            $params['referrer'],
            $params['method'],
            $params['headers'],
            $params['data']
        );

        $source = new DocumentSource($params['type'], $resource);
        $this->sourceQueue->enqueue($source);
    }
}
