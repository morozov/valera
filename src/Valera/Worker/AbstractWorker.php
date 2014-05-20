<?php

namespace Valera\Worker;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Queue;
use Valera\Queueable;
use Valera\Result;

/**
 * Abstract worker implementation. Defines the workflow.
 */
abstract class AbstractWorker implements WorkerInterface
{
    use LoggerAwareTrait;

    /**
     * Job queue
     *
     * @var \Valera\Queue
     */
    protected $queue;

    /**
     * @var \Valera\Queueable
     */
    protected $current;

    /**
     * Constructor
     *
     * @param \Valera\Queue            $queue  Job queue
     * @param \Psr\Log\LoggerInterface $logger Logger
     */
    public function __construct(Queue $queue, LoggerInterface $logger)
    {
        $this->queue = $queue;
        $this->setLogger($logger);

        register_shutdown_function(function () {
            if ($this->current) {
                $result = $this->createResult();
                $result->fail('Script unexpectedly terminated');
                $this->handleFailure($this->current, $result);
            }
        });
    }

    public function run()
    {
        $this->logger->info('Running worker');

        if (!count($this->queue)) {
            return 0;
        }

        $this->current = $item = $this->queue->dequeue();
        $hash = $item->getHash();

        $this->logger->info('Item #' . $hash . ' dequeued');

        $result = $this->createResult();
        $this->process($item, $result);
        if ($result->getStatus()) {
            $this->logger->info('Item #' . $hash . ' processed successfully');
            $this->handleSuccess($item, $result);
        } else {
            $this->logger->info('Processing item #' . $hash . ' failed');
            $this->handleFailure($item, $result);
        }

        // let the shutdown function know there's no item being processed
        $this->current = null;

        $this->logger->info('Stopping worker');

        return 1;
    }

    /**
     * @return Result
     */
    abstract protected function createResult();

    /**
     * Processes item and resolves the result accordingly
     *
     * @param \Valera\Queueable $item
     * @param \Valera\Result $result
     */
    abstract protected function process($item, $result);

    /**
     * Handles successful processing of the item
     *
     * @param \Valera\Queueable $item
     * @param \Valera\Result    $result
     */
    protected function handleSuccess($item, $result)
    {
        $this->logger->info(
            'Marking item #' . $item->getHash() . ' completed'
        );
        $this->queue->resolveCompleted($item);
    }

    /**
     * Handles item processing failure
     *
     * @param Queueable $item
     * @param Result    $result
     */
    protected function handleFailure($item, $result)
    {
        $this->logger->info(
            'Marking item #' . $this->current->getHash() . ' failed'
        );
        $this->queue->resolveFailed($item, $result->getReason());
    }
}
