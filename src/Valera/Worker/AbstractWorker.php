<?php

namespace Valera\Worker;

use Valera\Queue;
use Valera\Queueable;
use Valera\Result;

abstract class AbstractWorker
{
    /** @var Queue */
    protected $sourceQueue;
    protected $contentQueue;

    /**
     * @var \Valera\Queueable
     */
    protected $current;

    public function __construct()
    {
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
        $count = 0;
        $queue = $this->getQueue();
        while (count($queue) > 0) {
            $this->current = $item = $queue->dequeue();
            $result = $this->createResult();
            $this->process($item, $result);
            if ($result->getStatus()) {
                $this->handleSuccess($item, $result);
            } else {
                $this->handleFailure($item, $result);
            }
            $count++;

            // let the shutdown function know there's no item being processed
            $this->current = null;
        }

        return $count;
    }

    /**
     * @return Queue
     */
    abstract protected function getQueue();

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

    protected function handleSuccess($content, $result)
    {
        $this->getQueue()->resolveCompleted($content);
    }

    /**
     * Handles item processing failure
     *
     * @param Queueable $item
     * @param Result    $result
     */
    protected function handleFailure($item, $result)
    {
        $this->getQueue()->resolveFailed($item, $result->getReason());
    }
}
