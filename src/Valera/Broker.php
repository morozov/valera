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
     * @param \Valera\Worker\WorkerInterface $worker         Worker instance
     * @param \Valera\Worker\ResultHandler[] $resultHandlers Result prototype
     * @param \Valera\Queue\Resolver         $resolver
     * @param \Psr\Log\LoggerInterface       $logger         Logger
     */
    public function __construct(
        WorkerInterface $worker,
        array $resultHandlers,
        Resolver $resolver,
        LoggerInterface $logger
    ) {
        parent::__construct($resultHandlers, $resolver, $logger);
        $this->worker = $worker;
    }

    /** {@inheritDoc} */
    public function run(\Iterator $values)
    {
        foreach ($values as $object) {
            $this->process($object);
        }
    }

    protected function process($object)
    {
        list($item, $result) = $this->resolver->getItemAndResult($object);
        try {
            $this->worker->process($item, $result);
        } catch (\Exception $e) {
            $this->logger->error($e);
            $result->fail('Exception:' . $e->getMessage());
        }

        $this->handle($item, $result);
    }
}
