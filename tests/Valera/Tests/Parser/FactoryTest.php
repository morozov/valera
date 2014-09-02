<?php

namespace Valera\Tests\Parser;

use Valera\Parser\Factory;

/**
 * @covers \Valera\Parser\Factory
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
}
