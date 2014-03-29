<?php

namespace Valera\Tests\Result;

use Valera\Result\Proxy;

/**
 * @covers \Valera\Proxy
 */
class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Valera\Result\Proxy */
    private $proxy;

    protected function setUp()
    {
        $this->proxy = new Proxy();
    }

    public function testSuccess()
    {
        $data = array('foo' => 'bar');
        $this->proxy->succeed($data);

        /** @var \Valera\Result\Success $result */
        $result = $this->proxy->getResult();
        $this->assertEquals($data, $result->getData());
    }

    public function testFailure()
    {
        $message = 'Unable to find the title';
        $this->proxy->fail($message);

        /** @var \Valera\Result\Failure $result */
        $result = $this->proxy->getResult();
        $this->assertEquals($message, $result->getMessage());
    }

    /**
     * @expectedException \LogicException
     */
    public function testUnresolved()
    {
        $this->proxy->getResult();
    }

    /**
     * @expectedException \LogicException
     */
    public function testAlreadyResolved()
    {
        $this->proxy->fail('Failure1');
        $this->proxy->fail('Failure2');
    }
}
