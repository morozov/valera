<?php

namespace Valera\Tests\Value;

use Valera\Content;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testApi()
    {
        $source = Helper::getDocumentSource();
        $content = new Content('content-test', 'text/plain', $source);

        $this->assertEquals('content-test', $content->getContent());
        $this->assertEquals('text/plain', $content->getMimeType());
        $this->assertEquals($source, $content->getSource());
        $this->assertEquals($source->getType(), $content->getType());
        $this->assertEquals($source->getResource(), $content->getResource());
        $this->assertEquals($source->getHash(), $content->getHash());
    }
}
