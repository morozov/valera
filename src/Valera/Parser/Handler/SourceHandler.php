<?php

namespace Valera\Parser\Handler;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Queue\Writable;
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
     * @var \Valera\Queue\Writable
     */
    protected $sources;

    /**
     * Constructor
     *
     * @param \Valera\Queue\Writable       $sources
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Writable $sources,
        LoggerInterface $logger
    ) {
        $this->sources = $sources;
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
        $referrer = $content->getResource()->getUrl();

        /** @var \Valera\Parser\Result $result */
        foreach ($result->getReferences() as $source) {
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
            $params['payload']
        );

        $source = new DocumentSource($params['type'], $resource);
        $this->sources->enqueue($source);
    }
}
