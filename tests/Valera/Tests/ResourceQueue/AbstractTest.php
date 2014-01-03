<?php

namespace Valera\Tests\ResourceQueue;

use Valera\Resource;
use Valera\ResourceQueue;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Resource
     */
    protected $r1;

    /**
     * @var \Valera\Resource
     */
    protected $r2;

    /**
     * @var \Valera\ResourceQueue
     */
    protected $queue;

    protected function setUp()
    {
        $this->r1 = new Resource('http://example.com/');
        $this->r2 = new Resource('http://example.org/');
    }

    /**
     * @test
     */
    public function defaults()
    {
        $this->assertCount(0, $this->queue);
        $this->assertCount(0, $this->queue->getInProgress());
        $this->assertCount(0, $this->queue->getCompleted());
        $this->assertCount(0, $this->queue->getFailed());
    }

    /**
     * @test
     * @depends defaults
     */
    public function enqueue()
    {
        // resource is added to queue
        $this->queue->enqueue($this->r1);
        $this->assertCount(1, $this->queue);

        // queued resource is ignored
        $this->queue->enqueue($this->r1);
        $this->assertCount(1, $this->queue);

        // another resource is added
        $this->queue->enqueue($this->r2);
        $this->assertCount(2, $this->queue);
    }

    /**
     * @test
     * @depends enqueue
     */
    public function dequeue()
    {
        $this->queue->enqueue($this->r1);
        $this->queue->enqueue($this->r2);

        $r1 = $this->queue->dequeue();
        $this->assertContains($r1, $this->queue->getInProgress(), '', false, false);

        $this->queue->resolveCompleted($r1);
        $this->assertNotContains($r1, $this->queue->getInProgress(), '', false, false);
        $this->assertNotContains($r1, $this->queue->getFailed(), '', false, false);
        $this->assertContains($r1, $this->queue->getCompleted(), '', false, false);

        $r2 = $this->queue->dequeue();
        $this->queue->resolveFailed($r2);
        $this->assertNotContains($r2, $this->queue->getInProgress(), '', false, false);
        $this->assertNotContains($r2, $this->queue->getCompleted(), '', false, false);
        $this->assertContains($r2, $this->queue->getFailed(), '', false, false);
    }

    /**
     * @test
     * @depends enqueue
     * @depends dequeue
     */
    public function queuePreservesOrder()
    {
        $this->queue->enqueue($this->r1);
        $this->queue->enqueue($this->r2);

        $current = $this->queue->dequeue();
        $this->assertTrue($this->r1->equals($current));

        $current = $this->queue->dequeue();
        $this->assertTrue($this->r2->equals($current));
    }

    /**
     * @test
     * @depends defaults
     */
    public function dequeueEmpty()
    {
        $r = $this->queue->dequeue();
        $this->assertNull($r);
    }

    /**
     * @test
     * @depends enqueue
     * @expectedException \LogicException
     */
    public function resolveNotInProgress()
    {
        $this->queue->enqueue($this->r1);
        $this->queue->resolveCompleted($this->r1);
    }
}
