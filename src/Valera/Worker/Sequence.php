<?php

namespace Valera\Worker;

use Assert\Assertion;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Worker decorator which runs its nested workers in a sequence until any of them returns non-zero result
 */
class Sequence implements WorkerInterface
{
    use LoggerAwareTrait;

    /**
     * @var WorkerInterface[]
     */
    protected $workers;

    /**
     * Constructor
     *
     * @param WorkerInterface[]        $workers Nested workers
     * @param \Psr\Log\LoggerInterface $logger  Logger
     */
    public function __construct(array $workers, LoggerInterface $logger)
    {
        Assertion::allIsInstanceOf($workers, 'Valera\\Worker\\WorkerInterface');

        $this->workers = $workers;
        $this->logger = $logger;
    }

    /** {@inheritDoc} */
    public function run()
    {
        $totalCount = 0;
        $numWorkers = count($this->workers);
        $numIdles = 0;
        $workers = new \InfiniteIterator(new \ArrayIterator($this->workers));

        foreach ($workers as $name => $worker) {
            $this->logger->debug(sprintf('Running worker "%s"', $name));
            $count = $worker->run();
            if ($count) {
                $totalCount += $count;
                $numIdles = 0;
                $this->logger->debug(
                    sprintf('Worker "%s" processed %d item(s). Resetting idle counter', $name, $count)
                );
            } else {
                $numIdles++;
                $this->logger->debug(sprintf('%d of %d worker are idle', $numIdles, $totalCount));
            }

            if ($numIdles >= $numWorkers) {
                break;
            }
        }

        return $totalCount;
    }
}
