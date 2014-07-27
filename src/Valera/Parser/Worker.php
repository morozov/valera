<?php

namespace Valera\Parser;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Worker\WorkerInterface;

/**
 * Parser worker
 */
class Worker implements WorkerInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Valera\Parser\ParserInterface
     */
    protected $parser;

    /**
     * Constructor
     *
     * @param \Valera\Parser\ParserInterface $parser
     * @param \Psr\Log\LoggerInterface       $logger
     */
    public function __construct(
        ParserInterface $parser,
        LoggerInterface $logger
    ) {
        $this->parser = $parser;
        $this->setLogger($logger);
    }

    /**
     * Process single item
     *
     * @param \Valera\Content       $content
     * @param \Valera\Parser\Result $result
     */
    public function process($content, $result)
    {
        $this->logger->info('Parsing item #' . $content->getHash());
        $this->parser->parse($content, $result, $content->getResource());
    }
}
