<?php

namespace Valera\Tests\ResourceQueue;

use Valera\Resource;
use Valera\ResourceQueue;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\ResourceQueue
     */
    protected static $queue;

    /**
     * @var \Valera\Resource
     */
    protected static $r1;

    /**
     * @var \Valera\Resource
     */
    protected static $r2;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$queue->clean();
        
        self::$r1 = new Resource('index', 'http://example.com/');
        self::$r2 = new Resource('index', 'http://example.org/');
    }

    protected function tearDown()
    {
        self::$queue->clean();
    }

    /**
     * @test
     */
    public function defaults()
    {
        $this->assertCount(0, self::$queue);
        $this->assertCount(0, self::$queue->getInProgress());
        $this->assertCount(0, self::$queue->getCompleted());
        $this->assertCount(0, self::$queue->getFailed());
    }

    /**
     * @test
     * @depends defaults
     */
    public function enqueue()
    {
        // resource is added to queue
        self::$queue->enqueue(self::$r1);
        $this->assertCount(1, self::$queue);

        // queued resource is ignored
        self::$queue->enqueue(self::$r1);
        $this->assertCount(1, self::$queue);

        // another resource is added
        self::$queue->enqueue(self::$r2);
        $this->assertCount(2, self::$queue);
    }

    /**
     * @test
     * @depends enqueue
     */
    public function dequeue()
    {
        self::$queue->enqueue(self::$r1);
        self::$queue->enqueue(self::$r2);

        $r1 = self::$queue->dequeue();
        $this->assertContains($r1, self::$queue->getInProgress(), '', false, false);

        self::$queue->resolveCompleted($r1);
        $this->assertNotContains($r1, self::$queue->getInProgress(), '', false, false);
        $this->assertNotContains($r1, self::$queue->getFailed(), '', false, false);
        $this->assertContains($r1, self::$queue->getCompleted(), '', false, false);

        $r2 = self::$queue->dequeue();
        self::$queue->resolveFailed($r2);
        $this->assertNotContains($r2, self::$queue->getInProgress(), '', false, false);
        $this->assertNotContains($r2, self::$queue->getCompleted(), '', false, false);
        $this->assertContains($r2, self::$queue->getFailed(), '', false, false);
    }

    /**
     * @test
     * @depends enqueue
     * @depends dequeue
     */
    public function queuePreservesOrder()
    {
        self::$queue->enqueue(self::$r1);
        self::$queue->enqueue(self::$r2);

        $current = self::$queue->dequeue();
        $this->assertTrue(self::$r1->equals($current));

        $current = self::$queue->dequeue();
        $this->assertTrue(self::$r2->equals($current));
    }

    /**
     * @test
     * @depends defaults
     */
    public function dequeueEmpty()
    {
        $r = self::$queue->dequeue();
        $this->assertNull($r);
    }

    /**
     * @test
     * @depends enqueue
     * @expectedException \Valera\ResourceQueue\Exception\LogicException
     */
    public function resolveNotInProgress()
    {
        self::$queue->enqueue(self::$r1);
        self::$queue->resolveCompleted(self::$r1);
    }
}
