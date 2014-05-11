<?php

namespace Valera\Tests\Parser\Factory;

use Valera\Content;
use Valera\Parser\Factory\CallbackParser;
use Valera\Parser\Result;
use Valera\Resource;
use Valera\Source;

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
        $content = new Content(
            'test',
            new Source('test', new Resource('http://example.com'))
        );
        $result = new Result();
        $callback->expects($this->once())->method('__invoke')
            ->with($content, $result);
        $parser = new CallbackParser($callback);
        $parser->parse($content, $result);
    }
}
