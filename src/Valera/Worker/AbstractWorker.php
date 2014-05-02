<?php

namespace Valera\Worker;

use Valera\Queue;
use Valera\Result\Failure;
use Valera\Result\Proxy as ResultProxy;
use Valera\Result\ResultInterface;
use Valera\Result\Success;

abstract class AbstractWorker implements WorkerInterface
{
    /** @var Queue */
    protected $sourceQueue;
    protected $contentQueue;

    /**
     * @var \Valera\Queueable
     */
    protected $item;

    public function __construct()
    {
        register_shutdown_function(function() {
            if ($this->item) {
                $this->resolveFailed();
            }
        });
    }

    public function run()
    {
        $count = 0;
        $queue = $this->getQueue();
        while (count($queue) > 0) {
            $this->item = $queue->dequeue();
            $result = $this->process();
            $this->visit($result);
            $count++;

            // let the shutdown function know there's no item being processed
            $this->item = null;
        }

        return $count;
    }

    abstract protected function process();

    /**
     * @return Queue
     */
    abstract protected function getQueue();

    /**
     * @return ResultProxy
     */
    abstract protected function getResultProxy();

    public function visit(ResultInterface $result)
    {
        $result->accept($this);
    }

    public function visitSuccess(Success $result)
    {
        $this->getQueue()->resolveCompleted($this->item);
    }

    public function visitFailure(Failure $result)
    {
        $this->resolveFailed();
    }

    protected function resolveFailed()
    {
        $this->getQueue()->resolveFailed($this->item);
    }
}
