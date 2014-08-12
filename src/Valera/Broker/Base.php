<?php

namespace Valera\Broker;

use Assert\Assertion;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Valera\Queue\Resolver;
use Valera\Queueable;
use Valera\Worker\Result;

/**
 * Base broker implementation
 */
abstract class Base implements BrokerInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Valera\Worker\ResultHandler[]
     */
    protected $resultHandlers;

    /**
     * Queue resolver
     *
     * @var \Valera\Queue\Resolver
     */
    protected $resolver;

    /**
     * Constructor
     *
     * @param \Valera\Worker\ResultHandler[] $resultHandlers Result handlers
     * @param \Valera\Queue\Resolver         $resolver       Job queue resolver
     * @param \Psr\Log\LoggerInterface       $logger         Logger
     */
    public function __construct(
        array $resultHandlers,
        Resolver $resolver,
        LoggerInterface $logger
    ) {
        Assertion::allIsInstanceOf($resultHandlers, 'Valera\\Worker\\ResultHandler');

        $this->resolver = $resolver;
        $this->resultHandlers = $resultHandlers;
        $this->setLogger($logger);
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

        if ($result->getStatus()) {
            try {
                foreach ($this->resultHandlers as $handler) {
                    $handler->handle($item, $result);
                }

                $this->logger->info(
                    'Marking item #' . $hash . ' completed'
                );
            } catch (\LogicException $e) {
                $this->logger->error($e);
                $result->fail('Exception:' . $e->getMessage());
            }
        }

        $this->resolver->resolve($item, $result);
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
            'Marking item #' . $item->getHash() . ' failed'
        );
    }
}
