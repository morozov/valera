<?php

namespace Valera\Tests;

use Valera\Loader;

/**
 * @covers \Valera\Loader
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessResponse()
    {
        $response = $this->getResponseMock(false);
        $result = $this->getResultMock('resolve');
        $this->callProcessResponse($response, $result);
    }

    public function testFailedResponse()
    {
        $response = $this->getResponseMock(true);
        $result = $this->getResultMock('fail');
        $this->callProcessResponse($response, $result);
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
        $result = $this->getMock('Valera\Result\Proxy');
        $result->expects($this->once())
            ->method($expectedMethod);

        return $result;
    }

    private function callProcessResponse($response, $result)
    {
        $loader = $this->getMockBuilder('Valera\Loader')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $re = new \ReflectionMethod($loader, 'processResponse');
        $re->setAccessible(true);
        $re->invoke($loader, $response, $result);
    }
}