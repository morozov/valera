<?php

namespace Valera\Tests\Parser;

use Valera\Content;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result\Proxy;
use Valera\Parser\Facade;
use Valera\Resource;

/**
 * @covers \Valera\Parser\Facade
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementationIsCalled()
    {
        $content = $this->getContent();
        $result = new Proxy();

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
        $result = $this->getMockBuilder('Valera\Parser\Result\Proxy')
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
        $content = new Content('', '', $resource);

        return $content;
    }
}
