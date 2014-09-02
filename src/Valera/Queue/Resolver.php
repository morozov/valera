<?php

namespace Valera\Queue;

use Assert\Assertion;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Queue;
use Valera\Queueable;
use Valera\Worker\Result;

/**
 * Queue item resolver
 */
class Resolver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Valera\Queue
     */
    private $queue;

    /**
     * @var \Valera\Worker\Result
     */
    private $result;

    /**
     * @var \SplObjectStorage
     */
    private $storage;

    /**
     * Constructor
     *
     * @param \Valera\Queue                  $queue
     * @param \Valera\Worker\Result          $result
     * @param \Valera\Worker\ResultHandler[] $resultHandlers Result handlers
     * @param \Psr\Log\LoggerInterface       $logger         Logger
     */
    public function __construct(
        Queue $queue,
        Result $result,
        array $resultHandlers,
        LoggerInterface $logger
    ) {
        Assertion::allIsInstanceOf($resultHandlers, 'Valera\\Worker\\ResultHandler');

        $this->queue = $queue;
        $this->result = $result;
        $this->resultHandlers = $resultHandlers;
        $this->storage = new \SplObjectStorage();
        $this->setLogger($logger);

        register_shutdown_function(function () {
            $this->handleUnexpectedExit();
        });
    }

    /**
     * Receive update from iterator
     *
     * @param object $value
     * @param \Valera\Queueable $item
     */
    public function update($value, Queueable $item)
    {
        $this->attach($value, $item);
    }

    /**
     * Marks given item processing as successful
     *
     * @param object $task
     * @param callable $callback
     */
    public function resolve($task, callable $callback)
    {
        $result = $this->getResult();
        $callback($result);

        $item = $this->getItemByValue($task);
        $hash = $item->getHash();

        if ($result->getStatus()) {
            $this->handle($item, $result);
            $this->logger->info('Marking item #' . $hash . ' completed');
            $this->queue->resolveCompleted($item);
        } else {
            $reason = $result->getReason();
            $this->logger->info('Marking item #' . $hash . ' failed');
            $this->queue->resolveFailed($item, $reason);
        }

        $this->detach($task);
    }

    /**
     * Handles completed processing of the item
     *
     * @param \Valera\Queueable     $item
     * @param \Valera\Worker\Result $result
     */
    protected function handle($item, $result)
    {
        $hash = $item->getHash();
        $this->logger->info('Running post-processors for item #' . $hash);

        try {
            foreach ($this->resultHandlers as $handler) {
                $handler->handle($item, $result);
            }
        } catch (\LogicException $e) {
            $this->logger->error($e);
            $result->fail('Exception:' . $e->getMessage());
        }
    }

    /**
     * Creates relation between dequeued value and corresponding queue item and its processing result
     *
     * @param object $value
     * @param \Valera\Queueable $item
     */
    protected function attach($value, Queueable $item)
    {
        if (!$this->storage->contains($value)) {
            $this->storage->attach($value, $item);
        }
    }

    /**
     * Destroys relation between dequeued item and its processing result
     *
     * @param object $value
     */
    protected function detach($value)
    {
        $this->storage->detach($value);
    }

    /**
     * Returns result corresponding to the given item
     *
     * @param object $value
     *
     * @return \Valera\Queueable $item
     * @throws \Exception
     */
    public function getItemByValue($value)
    {
        if (!$this->storage->offsetExists($value)) {
            throw new \Exception();
        }

        return $this->storage->offsetGet($value);
    }

    public function getResult()
    {
        return clone $this->result;
    }

    protected function handleUnexpectedExit()
    {
        foreach ($this->storage as $value) {
            $this->resolve($value, function (Result $result) {
                $result->fail('Script unexpectedly terminated');
            });
        }
    }
}
