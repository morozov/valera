<?php

namespace Valera\Tests\Queue;

use Valera\Resource;
use Valera\Source;
use Valera\Queue;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Queue
     */
    protected static $queue;

    /**
     * @var \Valera\Source
     */
    protected static $s1;

    /**
     * @var \Valera\Source
     */
    protected static $s2;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$queue->clean();

        self::$s1 = new Source(new Resource('http://example.com/'), '');
        self::$s2 = new Source(new Resource('http://example.org/'), '');
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
        self::$queue->enqueue(self::$s1);
        $this->assertCount(1, self::$queue);

        // queued resource is ignored
        self::$queue->enqueue(self::$s1);
        $this->assertCount(1, self::$queue);

        // another resource is added
        self::$queue->enqueue(self::$s2);
        $this->assertCount(2, self::$queue);
    }

    /**
     * @test
     * @depends enqueue
     */
    public function dequeue()
    {
        self::$queue->enqueue(self::$s1);
        self::$queue->enqueue(self::$s2);

        $s1 = self::$queue->dequeue();
        $this->assertContains($s1, self::$queue->getInProgress(), '', false, false);

        self::$queue->resolveCompleted($s1);
        $this->assertNotContains($s1, self::$queue->getInProgress(), '', false, false);
        $this->assertNotContains($s1, self::$queue->getFailed(), '', false, false);
        $this->assertContains($s1, self::$queue->getCompleted(), '', false, false);

        $s2 = self::$queue->dequeue();
        self::$queue->resolveFailed($s2);
        $this->assertNotContains($s2, self::$queue->getInProgress(), '', false, false);
        $this->assertNotContains($s2, self::$queue->getCompleted(), '', false, false);
        $this->assertContains($s2, self::$queue->getFailed(), '', false, false);
    }

    /**
     * @test
     * @depends enqueue
     * @depends dequeue
     */
    public function queuePreservesOrder()
    {
        self::$queue->enqueue(self::$s1);
        self::$queue->enqueue(self::$s2);

        $current = self::$queue->dequeue();
        $this->assertEquals(self::$s1->getHash(), $current->getHash());

        $current = self::$queue->dequeue();
        $this->assertEquals(self::$s2->getHash(), $current->getHash());
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
     * @expectedException Valera\Queue\Exception\LogicException
     */
    public function resolveNotInProgress()
    {
        self::$queue->enqueue(self::$s1);
        self::$queue->resolveCompleted(self::$s1);
    }
}
