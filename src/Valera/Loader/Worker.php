<?php

namespace Valera\Loader;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Worker\WorkerInterface;

/**
 * Loader worker
 */
class Worker implements WorkerInterface
{
    use LoggerAwareTrait;

    /**
     * Loader implementation
     *
     * @var \Valera\Loader\LoaderInterface
     */
    protected $loader;

    /**
     * Constructor
     *
     * @param \Valera\Loader\LoaderInterface $loader
     * @param \Psr\Log\LoggerInterface       $logger
     */
    public function __construct(
        LoaderInterface $loader,
        LoggerInterface $logger
    ) {
        $this->loader = $loader;
        $this->setLogger($logger);
    }

    /**
     * Processes source and resolves the result accordingly
     *
     * @param \Valera\Source $source
     * @param \Valera\Loader\Result $result
     */
    public function process($source, $result)
    {
        $resource = $source->getResource();
        $this->logger->info('Downloading ' . $resource->getUrl());
        $this->loader->load($resource, $result);
    }
}
