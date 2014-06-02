<?php

namespace Valera\Tests\Parser\Factory;

use Valera\Parser\Factory\CallbackParser;
use Valera\Parser\Result;
use Valera\Tests\Value\Helper;

/**
 * @covers \Valera\Parser\Factory\CallbackParser
 * @uses \Valera\Content
 * @uses \Valera\Resource
 * @uses \Valera\Source
 */
class CallbackParserTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementationIsCalled()
    {
        $callback = $this->getMockBuilder('stdClass')
            ->setMethods(array('__invoke'))
            ->getMock();
        $content = Helper::getContent();
        $result = new Result();
        $callback->expects($this->once())->method('__invoke')
            ->with($content, $result);
        $parser = new CallbackParser($callback);
        $parser->parse($content, $result);
    }
}
