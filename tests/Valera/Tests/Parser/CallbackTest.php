<?php

namespace Valera\Tests\Parser;

use Valera\Content;
use Valera\Parser\Callback;
use Valera\Parser\Result;
use Valera\Resource;
use Valera\Source;

/**
 * @covers \Valera\Parser\Callback
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
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
        $parser = new Callback($callback);
        $parser->parse($content, $result);
    }
}
