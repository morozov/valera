<?php

namespace Valera\Tests;

use Valera\Broker;
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
     * @var \Valera\Broker
     */
    private $broker;

    public function setUp()
    {
        $this->queue = $this->getMock('Valera\\Queue');
        $this->worker = $this->getMock('Valera\\Worker\\WorkerInterface');
        $this->handler = $this->getMock('Valera\\Worker\\ResultHandler');
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->broker = new Broker($this->queue, $this->worker, new Result(), array($this->handler), $logger);
    }

    /** @test */
    public function emptyQueue()
    {
        $this->setQueueCount(0);

        $this->assertEquals(0, $this->broker->run());
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
    public function logicException()
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

        $this->assertEquals(1, $this->broker->run());
    }

    /**
     * @return \Valera\Queueable
     */
    private function getItem()
    {
        return $this->getMock('Valera\\Queueable');
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
