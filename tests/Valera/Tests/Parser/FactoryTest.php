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
        $parser1 = $this->factory->getParser('parser1');
        $this->assertInstanceOf('\\Valera\\Tests\\Parser\\Factory\\Parser1', $parser1);

        $parser2 = $this->factory->getParser('parser2');
        $this->assertNull($parser2);
    }

    /**
     * @expectedException \UnexpectedValueException
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
