<?php

namespace Valera\Tests\Parser\PostProcessor;

use Valera\Entity\Document;
use Valera\Parser\PostProcessor\LogNulls as Processor;

/**
 * @covers \Valera\Parser\PostProcessor\LogNulls
 * @uses \Valera\Entity\Document
 */
class LogNullsTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function trim()
    {
        $document = new Document('test', array(
            'foo' => array(
                'bar' => null,
            ),
            'baz' => null,
        ));

        /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $logger->expects($this->exactly(2))
            ->method('warning');

        $processor = new Processor($logger);
        $processor->process($document);
    }
}
