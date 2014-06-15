<?php

namespace Valera\Tests\Queue;

use Valera\Queue;
use Valera\Tests\Value\Helper;

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

        self::$s1 = Helper::getDocumentSource();
        self::$s2 = Helper::getAnotherDocumentSource();
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
        $this->assertCollectionContains($s1, self::$queue->getInProgress());

        self::$queue->resolveCompleted($s1);
        $this->assertCollectionNotContains($s1, self::$queue->getInProgress());
        $this->assertCollectionNotContains($s1, self::$queue->getFailed());
        $this->assertCollectionContains($s1, self::$queue->getCompleted());

        $s2 = self::$queue->dequeue();
        self::$queue->resolveFailed($s2, 'Failure reason');
        $this->assertCollectionNotContains($s2, self::$queue->getInProgress());
        $this->assertCollectionNotContains($s2, self::$queue->getCompleted());
        $this->assertCollectionContains($s2, self::$queue->getFailed());
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
     * @expectedException \Valera\Queue\Exception\LogicException
     */
    public function resolveNotInProgress()
    {
        self::$queue->enqueue(self::$s1);
        self::$queue->resolveCompleted(self::$s1);
    }

    /**
     * @test
     * @depends enqueue
     * @depends dequeue
     */
    public function enqueueInProgress()
    {
        self::$queue->enqueue(self::$s1);
        self::$queue->dequeue();
        self::$queue->enqueue(self::$s1);

        // make sure item is not added twice
        $this->assertCount(0, self::$queue);

        // make sure item is still in progress
        $this->assertCollectionContains(self::$s1, self::$queue->getInProgress());
    }

    /**
     * @test
     * @depends enqueue
     * @depends dequeue
     */
    public function enqueueCompleted()
    {
        self::$queue->enqueue(self::$s1);
        $s = self::$queue->dequeue();
        self::$queue->resolveCompleted($s);
        self::$queue->enqueue(self::$s1);

        // make sure item is not added twice
        $this->assertCount(0, self::$queue);

        // make sure item is still completed
        $this->assertCollectionContains(self::$s1, self::$queue->getCompleted());
    }

    /**
     * @test
     * @depends enqueue
     * @depends dequeue
     */
    public function enqueueFailed()
    {
        self::$queue->enqueue(self::$s1);
        $s = self::$queue->dequeue();
        self::$queue->resolveFailed($s, 'Failure reason');
        self::$queue->enqueue(self::$s1);

        // make sure item is not added twice
        $this->assertCount(0, self::$queue);

        // make sure item is still failed
        $this->assertCollectionContains(self::$s1, self::$queue->getFailed());
    }

    private function assertCollectionContains($needle, $haystack, $message = '')
    {
        $this->assertContains($needle, $haystack, $message, false, false);
    }

    private function assertCollectionNotContains($needle, $haystack, $message = '')
    {
        $this->assertNotContains($needle, $haystack, $message, false, false);
    }
}
