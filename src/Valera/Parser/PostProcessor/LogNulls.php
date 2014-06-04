<?php

namespace Valera\Parser\PostProcessor;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Entity\Document;
use Valera\Parser\PostProcessor;

/**
 * Logs occurrences of NULLs in document data
 */
class LogNulls implements PostProcessor
{
    use LoggerAwareTrait;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    /**
     * {@inheritDoc}
     */
    public function process(Document $document)
    {
        $document->iterate(function ($value) {
            return $value === null;
        }, function ($value, array $path) use ($document) {
            $this->logger->warning(sprintf(
                'The %s property of document #%s contains %s',
                implode('.', $path),
                $document->getId(),
                var_export($value, true)
            ));
        });
    }
}
