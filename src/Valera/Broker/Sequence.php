<?php

namespace Valera\Broker;

use Assert\Assertion;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Broker decorator which runs its nested brokers in a sequence until any of them returns non-zero result
 */
class Sequence implements BrokerInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Valera\Broker\BrokerInterface[]
     */
    protected $brokers;

    /**
     * Constructor
     *
     * @param \Valera\Broker\BrokerInterface[] $brokers Nested brokers
     * @param \Psr\Log\LoggerInterface         $logger  Logger
     */
    public function __construct(array $brokers, LoggerInterface $logger)
    {
        Assertion::allIsInstanceOf($brokers, 'Valera\\Broker\\BrokerInterface');

        $this->brokers = $brokers;
        $this->logger = $logger;
    }

    /** {@inheritDoc} */
    public function run(\Iterator $items)
    {
        $totalCount = 0;
        $numBrokers = count($this->brokers);
        $numIdles = 0;
        $brokers = new \InfiniteIterator(new \ArrayIterator($this->brokers));

        foreach ($brokers as $name => $broker) {
            $this->logger->debug(sprintf('Running broker "%s"', $name));
            $count = $broker->run();
            if ($count) {
                $totalCount += $count;
                $numIdles = 0;
                $this->logger->debug(
                    sprintf('Broker "%s" processed %d item(s). Resetting idle counter', $name, $count)
                );
            } else {
                $numIdles++;
                $this->logger->debug(sprintf('%d of %d broker are idle', $numIdles, $numBrokers));
            }

            if ($numIdles >= $numBrokers) {
                break;
            }
        }

        return $totalCount;
    }
}
