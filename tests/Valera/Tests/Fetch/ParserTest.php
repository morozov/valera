<?php

namespace Valera\Tests;
use Valera\Worker\Parser;
use Valera\Worker\ParserFactoryInterface;
use Valera\Resource;
use Valera\Content;


class Factory implements ParserFactoryInterface
{

    public function getParser($type)
    {
        if ($type == 'page') {
            return function ($content) {
                $parsed = strip_tags($content);
                return $parsed;
            };
        } else {
            return function ($content) {
                return false;
            };
        }

    }
}

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessRun()
    {
        $testContentString = "<html><body><h1>Hello Valera</h1></body></html>";

        $fakeResource = $this->getMockBuilder('\Valera\Resource')->disableOriginalConstructor()->getMock();
        $fakeResource->expects($this->any())->method('getType')->will($this->returnValue('page'));

        $content = new Content($testContentString, $fakeResource);
        $parser = new Parser(new Factory());

        $successCallback = function ($result) {
            echo "RESULT: " . $result;
        };

        $this->expectOutputString('RESULT: Hello Valera');
        $parser->addJob($content)
            ->setSuccessCallback($successCallback)
            ->run();
    }

    public function testFailureRun()
    {
        $testContentString = "<html><body><h1>Hello Valera</h1></body></html>";

        $fakeResource = $this->getMockBuilder('\Valera\Resource')->disableOriginalConstructor()->getMock();
        $fakeResource->expects($this->any())->method('getType')->will($this->returnValue('unknown'));

        $content = new Content($testContentString, $fakeResource);
        $parser = new Parser(new Factory());

        $failCallback = function () {
            echo "Something goes wrong";
        };

        $this->expectOutputString('Something goes wrong');
        $parser->addJob($content)
            ->setFailureCallback($failCallback)
            ->run();
    }
}
