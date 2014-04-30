<?php

namespace Valera\Tests;

use Valera\Loader;
use Valera\Resource;
use Valera\Source;

/**
 * @covers \Valera\Loader
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->markTestIncomplete();
    }

    public function testSuccessResponse()
    {
        $response = $this->getResponseMock(false);
        $result = $this->getResultMock('resolve');
        $source = $this->getSource();
        $this->callProcessResponse($response, $result, $source);
    }

    public function testFailedResponse()
    {
        $response = $this->getResponseMock(true);
        $result = $this->getResultMock('fail');
        $source = $this->getSource();
        $this->callProcessResponse($response, $result, $source);
    }

    private function getResponseMock($isError)
    {
        $response = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('isError'))
            ->getMock();
        $response->expects($this->any())
            ->method('isError')
            ->will($this->returnValue($isError));

        return $response;
    }

    private function getResultMock($expectedMethod)
    {
        $result = $this->getMockBuilder('Valera\Loader\Result\Proxy')
            ->disableOriginalConstructor()
            ->getMock();
        $result->expects($this->once())
            ->method($expectedMethod);

        return $result;
    }

    private function getSource()
    {
        $resource = new Resource('http://example.com/');
        $source = new Source('example', $resource);

        return $source;
    }

    private function callProcessResponse($response, $result, $source)
    {
        $loader = $this->getMockBuilder('Valera\Loader')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $re = new \ReflectionMethod($loader, 'processResponse');
        $re->setAccessible(true);
        $re->invoke($loader, $response, $result, $source);
    }
}
