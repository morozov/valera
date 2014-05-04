<?php

namespace Valera\Tests\Parser;

use Valera\Content;
use Valera\Parser\ParserInterface;
use Valera\Parser\Facade;
use Valera\Resource;
use Valera\Source;

/**
 * @covers \Valera\Parser\Facade
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementationIsCalled()
    {
        $content = $this->getContent();
        $result = $this->getMockBuilder('Valera\Parser\Result')
            ->disableOriginalConstructor()
            ->getMock();

        $parser = $this->getMockBuilder('Valera\Parser\ParserInterface')
            ->getMock();
        $parser->expects($this->once())
            ->method('parse')
            ->with($content, $result);

        $factory = $this->getFactory($parser);

        $facade = new Facade($factory);
        $facade->parse($content, $result);
    }

    public function testUnknownTypeResultsToFailure()
    {
        $content = $this->getContent();
        $result = $this->getMockBuilder('Valera\Parser\Result')
            ->disableOriginalConstructor()
            ->setMethods(array('fail'))
            ->getMock();
        $result->expects($this->once())
            ->method('fail');

        $factory = $this->getFactory(null);

        $facade = new Facade($factory);
        $facade->parse($content, $result);
    }

    private function getFactory(ParserInterface $parser = null)
    {
        $factory = $this->getMockBuilder('Valera\Parser\FactoryInterface')
            ->getMock();
        $factory->expects($this->any())
            ->method('getParser')
            ->will($this->returnValue($parser));

        return $factory;
    }

    private function getContent()
    {
        $resource = new Resource('http://example.com/');
        $source = new Source('', $resource);
        $content = new Content('', $source);

        return $content;
    }
}
