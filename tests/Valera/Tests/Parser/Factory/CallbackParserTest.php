<?php

namespace Valera\Tests\Parser\Factory;

use Valera\Parser\Factory\CallbackParser;
use Valera\Parser\Result;
use Valera\Tests\Serializer\Helper;

/**
 * @covers \Valera\Parser\Factory\CallbackParser
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
