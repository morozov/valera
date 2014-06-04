<?php

namespace Valera\Tests\Parser\PostProcessor;

use Valera\Entity\Document;
use Valera\Parser\PostProcessor\FixNonUtf8 as Processor;

/**
 * @covers \Valera\Parser\PostProcessor\FixNonUtf8
 * @uses \Valera\Entity\Document
 */
class FixNonUtf8Test extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function trim()
    {
        $document = new Document('test', array(
            'foo' => array(
                'bar' => "Ва\x00ле\x01ра\x02",
            ),
        ));

        /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');

        $processor = new Processor($logger);
        $processor->process($document);

        $data = $document->getData();
        $this->assertEquals('Ва?ле?ра?', $data['foo']['bar']);
    }
}
