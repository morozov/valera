<?php

namespace Valera\Tests;

use Valera\Broker;
use Valera\Queue\Resolver;
use Valera\Queueable;
use Valera\Worker\Result;

/**
 * @covers \Valera\Broker
 * @uses \Valera\Worker\Result
 */
class BrokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Queue|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queue;

    /**
     * @var \Valera\Worker\WorkerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $worker;

    /**
     * @var \Valera\Worker\ResultHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private $handler;

    /**
     * @var \Valera\Queue\Resolver
     */
    private $resolver;

    /**
     * @var \Valera\Broker
     */
    private $broker;

    public function setUp()
    {
        $this->markTestIncomplete('To be refactored');

        $this->queue = $this->getMock('Valera\\Queue');
        $this->worker = $this->getMock('Valera\\Worker\\WorkerInterface');
        $this->handler = $this->getMock('Valera\\Worker\\ResultHandler');
        $this->resolver = new Resolver($this->queue, new Result());

        /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->broker = new Broker($this->queue, $this->worker, array($this->handler), $this->resolver, $logger);
    }

    /** @test */
    public function emptyQueue()
    {
        $this->assertEquals(0, $this->runBroker(array()));
    }

    /** @test */
    public function success()
    {
        /** @var \Valera\Queueable $item */
        $item = $this->getMock('Valera\\Queueable');
        $this->enqueueItem($item);
        $this->setQueueCount(1);

        $this->handler->expects($this->once())
            ->method('handle')
            ->with($item, $this->anything());

        $this->queue->expects($this->once())
            ->method('resolveCompleted')
            ->with($item);

        $this->processItem($item, function (Result $result) {
            $result->resolve();
        });
    }

    /** @test */
    public function failure()
    {
        $item = $this->getItem();
        $this->enqueueItem($item);
        $this->setQueueCount(1);

        $this->handler->expects($this->never())
            ->method('handle');

        $this->queue->expects($this->once())
            ->method('resolveFailed')
            ->with($item, 'The reason');

        $this->processItem($item, function (Result $result) {
            $result->fail('The reason');
        });
    }

    /** @test */
    public function workerException()
    {
        $item = $this->getItem();
        $this->enqueueItem($item);
        $this->setQueueCount(1);

        $this->handler->expects($this->never())
            ->method('handle');

        $this->queue->expects($this->once())
            ->method('resolveFailed')
            ->with($item, $this->stringStartsWith('Exception'));

        $this->processItem($item, function () {
            throw new \LogicException;
        });
    }

    /** @test */
    public function handlerException()
    {
        $item = $this->getItem();
        $this->enqueueItem($item);
        $this->setQueueCount(1);

        $this->handler->expects($this->once())
            ->method('handle')
            ->will($this->throwException(new \LogicException));

        $this->queue->expects($this->once())
            ->method('resolveFailed')
            ->with($item, $this->stringStartsWith('Exception'));

        $this->processItem($item, function (Result $result) {
            $result->resolve();
        });
    }

    /** @test */
    public function handleUnexpectedExit()
    {
        $item = $this->getItem();

        $this->queue->expects($this->once())
            ->method('resolveFailed')
            ->with($item);

        $reCurrent = new \ReflectionProperty($this->broker, 'current');
        $reCurrent->setAccessible(true);
        $reCurrent->setValue($this->broker, $item);

        $reHandle = new \ReflectionMethod($this->broker, 'handleUnexpectedExit');
        $reHandle->setAccessible(true);
        $reHandle->invoke($this->broker);
    }

    private function processItem(Queueable $item, callable $behavior)
    {
        $this->worker->expects($this->once())
            ->method('process')
            ->with($item, $this->anything())
            ->will($this->returnCallback(function ($_, Result $result) use ($behavior) {
                $behavior($result);
            }));

        $this->assertEquals(1, $this->runBroker(array($item)));
    }

    /**
     * @return \Valera\Queueable
     */
    private function getItem()
    {
        return $this->getMock('Valera\\Queueable');
    }

    private function runBroker(array $items)
    {
        $iterator = new \ArrayIterator($items);
        $re = new \ReflectionMethod($this->broker, 'runIterator');
        $re->setAccessible(true);
        return $re->invoke($this->broker, $iterator);
    }

    private function setQueueCount($count)
    {
        $this->queue->expects($this->once())
            ->method('count')
            ->will($this->returnValue($count));
    }

    private function enqueueItem(Queueable $item)
    {
        $this->queue->expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue($item));
    }
}
