<?php

namespace Valera\Tests\Parser\PostProcessor;

use Valera\Entity\Document;
use Valera\Parser\PostProcessor\Trim as Processor;

/**
 * @covers \Valera\Parser\PostProcessor\Trim
 * @uses \Valera\Entity\Document
 */
class TrimTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function trim()
    {
        $document = new Document('trim-test', array(
            'foo' => '  bar  ',
        ));

        $processor = new Processor();
        $processor->process($document);

        $data = $document->getData();
        $this->assertEquals('bar', $data['foo']);
    }
}
