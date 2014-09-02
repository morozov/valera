<?php

namespace Valera;

use Psr\Log\LoggerInterface;
use Valera\Broker\Base;
use Valera\Queue\Resolver;
use Valera\Worker\WorkerInterface;

/**
 * Default broker implementation
 */
class Broker extends Base
{
    /**
     * @var \Valera\Worker\WorkerInterface
     */
    protected $worker;

    /**
     * Constructor
     *
     * @param \Valera\Queue                  $queue          Source queue
     * @param callable|null                  $converter      Queued item converter
     * @param \Valera\Worker\WorkerInterface $worker         Worker instance
     * @param \Valera\Worker\ResultHandler[] $resultHandlers Result prototype
     * @param \Valera\Queue\Resolver         $resolver
     * @param \Psr\Log\LoggerInterface       $logger         Logger
     */
    public function __construct(
        Queue $queue,
        callable $converter = null,
        WorkerInterface $worker,
        array $resultHandlers,
        Resolver $resolver,
        LoggerInterface $logger
    ) {
        parent::__construct($queue, $converter, $resultHandlers, $resolver, $logger);
        $this->worker = $worker;
    }

    /** {@inheritDoc} */
    protected function runIterator(\Iterator $values)
    {
        $count = 0;
        foreach ($values as $value) {
            $this->process($value);
            $count++;
        }

        return $count;
    }

    protected function process($value)
    {
        $item = $this->resolver->getItemByValue($value);
        $result = $this->resolver->getResult();
        try {
            $this->worker->process($value, $result);
        } catch (\Exception $e) {
            $this->logger->error($e);
            $result->fail('Exception:' . $e->getMessage());
        }

        $this->handle($value, $item, $result);
    }
}
