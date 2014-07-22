<?php

namespace Valera\Tests\Parser\Factory;

use Valera\Parser\Factory\CallbackParser;
use Valera\Parser\Result;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Parser\Factory\CallbackParser
 * @uses \Valera\Content
 * @uses \Valera\Resource
 * @uses \Valera\Value\ResourceData
 * @uses \Valera\Source
 * @uses \Valera\Source\DocumentSource
 */
class CallbackParserTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementationIsCalled()
    {
        $callback = $this->getMockBuilder('stdClass')
            ->setMethods(array('__invoke'))
            ->getMock();
        $content = Helper::getContent();
        $resource = $content->getResource();
        $result = new Result();
        $callback->expects($this->once())->method('__invoke')
            ->with($content, $result, $resource);
        $parser = new CallbackParser($callback);
        $parser->parse($content, $result, $resource);
    }
}
