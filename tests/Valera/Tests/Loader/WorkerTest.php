<?php

namespace Valera\Tests\Loader;

use Valera\Loader\Worker;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Loader\Worker
 * @uses \Valera\Resource
 * @uses \Valera\Source
 */
class WorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Valera\Loader\LoaderInterface
     */
    private $loader;

    /**
     * @var \Valera\Loader\Worker
     */
    private $worker;

    protected function setUp()
    {
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->loader = $this->getMock('Valera\\Loader\\LoaderInterface');
        $this->worker = new Worker($this->loader, $logger);
    }

    /** @test */
    public function process()
    {
        $source = Helper::getSource();
        $result = $this->getMock('Valera\\Loader\\Result');
        $this->loader->expects($this->once())
            ->method('load')
            ->with($source->getResource(), $result);
        $this->worker->process($source, $result);
    }
}
