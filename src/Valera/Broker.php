<?php

namespace Valera;

use Assert\Assertion;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Broker\BrokerInterface;
use Valera\Queue;
use Valera\Worker\Result;
use Valera\Worker\WorkerInterface;

/**
 * Default broker implementation
 */
class Broker implements BrokerInterface
{
    use LoggerAwareTrait;

    /**
     * Job queue
     *
     * @var \Valera\Queue
     */
    protected $queue;

    /**
     * @var \Valera\Worker\WorkerInterface
     */
    protected $worker;

    /**
     * Result prototype
     *
     * @var \Valera\Worker\Result
     */
    protected $result;

    /**
     * @var \Valera\Worker\ResultHandler[]
     */
    private $resultHandlers;

    /**
     * @var \Valera\Queueable
     */
    protected $current;

    /**
     * Constructor
     *
     * @param \Valera\Queue                  $queue          Job queue
     * @param \Valera\Worker\WorkerInterface $worker         Worker instance
     * @param \Valera\Worker\Result          $result         Result prototype
     * @param \Valera\Worker\ResultHandler[] $resultHandlers Result prototype
     * @param \Psr\Log\LoggerInterface       $logger         Logger
     */
    public function __construct(
        Queue $queue,
        WorkerInterface $worker,
        Result $result,
        array $resultHandlers,
        LoggerInterface $logger
    ) {
        Assertion::allIsInstanceOf($resultHandlers, 'Valera\\Worker\\ResultHandler');

        $this->queue = $queue;
        $this->worker = $worker;
        $this->result = $result;
        $this->resultHandlers = $resultHandlers;
        $this->setLogger($logger);

        register_shutdown_function(function () {
            $this->handleUnexpectedExit();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function run($maxItems = null)
    {
        \Assert\that($maxItems)->nullOr()->integer()->min(1);

        $this->logger->info('Running worker');

        if (!count($this->queue)) {
            return 0;
        }

        $this->current = $item = $this->queue->dequeue();
        $hash = $item->getHash();

        $this->logger->info('Item #' . $hash . ' dequeued');

        $result = $this->createResult();

        try {
            $this->worker->process($item, $result);
            if ($result->getStatus()) {
                $this->logger->info('Item #' . $hash . ' processed successfully');
                $this->handleSuccess($item, $result);
            }
        } catch (\LogicException $e) {
            $this->logger->error($e);
            $result->fail('Exception:' . $e->getMessage());
        }

        if (!$result->getStatus()) {
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
    protected function createResult()
    {
        return clone $this->result;
    }

    /**
     * Handles successful processing of the item
     *
     * @param \Valera\Queueable $item
     * @param \Valera\Worker\Result    $result
     */
    protected function handleSuccess($item, $result)
    {
        foreach ($this->resultHandlers as $handler) {
            $handler->handle($item, $result);
        }

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

    private function handleUnexpectedExit()
    {
        if ($this->current) {
            $result = $this->createResult();
            $result->fail('Script unexpectedly terminated');
            $this->handleFailure($this->current, $result);
        }
    }
}
