<?php

namespace Valera\Tests\Parser;

use Valera\Parser\Factory;

/**
 * @covers \Valera\Parser\Factory
 * @uses \Valera\Parser\Factory\CallbackParser
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Valera\Parser\Factory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new Factory(array(
            'Valera\\Tests\\Parser\\Factory'
        ));
    }

    public function testParserLoaded()
    {
        $parser = $this->factory->getParser('some-parser');
        $this->assertInstanceOf('\\Valera\\Tests\\Parser\\Factory\\SomeParser', $parser);

        $parser = $this->factory->getParser('unknown-parser');
        $this->assertNull($parser);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadInterface()
    {
        $this->factory->getParser('badInterface');
    }

    public function testParserIsInstantiatedOnce()
    {
        $factory = $this->getMockBuilder('Valera\\Parser\\Factory')
            ->setMethods(array('loadParser'))
            ->getMock();

        $instance = $this->getMockBuilder('Valera\\Parser\\ParserInterface')
            ->getMock();

        $factory->expects($this->once())
            ->method('loadParser')
            ->will($this->returnValue($instance));

        $factory->getParser('parser');
        $factory->getParser('parser');
    }

    /** @test */
    public function registerParser()
    {
        $adapter1 = $this->getMockBuilder('Valera\\Parser\\AdapterInterface')
            ->setMethods(array('supports', 'wrap'))
            ->getMock();
        $adapter1->expects($this->any())
            ->method('supports')
            ->willReturn(false);
        $adapter1->expects($this->never())
            ->method('wrap');

        $adapter2 = $this->getMockBuilder('Valera\\Parser\\AdapterInterface')
            ->setMethods(array('supports', 'wrap'))
            ->getMock();
        $adapter2->expects($this->any())
            ->method('supports')
            ->willReturn(true);
        $adapter2->expects($this->once())
            ->method('wrap')
            ->willReturn(function () {
            });

        $this->factory->registerAdapter($adapter1);
        $this->factory->registerAdapter($adapter2);

        // adapter found
        $parser1 = $this->factory->registerParser('test', null);
        $this->assertInstanceOf('Valera\\Parser\\Factory\\CallbackParser', $parser1);

        // adapter not found
        $this->factory->unregisterAdapter($adapter2);
        $this->setExpectedException('\InvalidArgumentException');
        $this->factory->registerParser('test', null);
    }
}
