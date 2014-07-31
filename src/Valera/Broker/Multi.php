<?php

namespace Valera\Broker;

use Assert\Assertion;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Queue;
use Valera\Queue\Iterator;
use Valera\Queueable;
use Valera\Worker\Result;
use Valera\Worker\Multi as Worker;

/**
 * Default broker implementation
 */
class Multi implements BrokerInterface
{
    use LoggerAwareTrait;

    /**
     * Job queue
     *
     * @var \Valera\Queue
     */
    protected $queue;

    /**
     * @var \Valera\Worker\Multi
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
     * @var \Valera\Queueable[]
     */
    protected $current = array();

    /**
     * Constructor
     *
     * @param \Valera\Queue                  $queue          Job queue
     * @param \Valera\Worker\Multi           $worker         Worker instance
     * @param \Valera\Worker\Result          $result         Result prototype
     * @param \Valera\Worker\ResultHandler[] $resultHandlers Result prototype
     * @param \Psr\Log\LoggerInterface       $logger         Logger
     */
    public function __construct(
        Queue $queue,
        Worker $worker,
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

    public function run()
    {
        $this->logger->info('Running worker');

        $iterator = new Iterator($this->queue);
        $this->worker->processMulti($iterator, function (Queueable $item, Result $result) {
            $hash = $item->getHash();
            if ($result->getStatus()) {
                try {
                    $this->logger->info('Item #' . $hash . ' processed successfully');
                    $this->handleSuccess($item, $result);
                } catch (\LogicException $e) {
                    $this->logger->error($e);
                    $result->fail('Exception:' . $e->getMessage());
                }
            }

            if (!$result->getStatus()) {
                $this->logger->info('Processing item #' . $hash . ' failed');
                $this->handleFailure($item, $result);
            }
        });
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
