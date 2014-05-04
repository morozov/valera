<?php

namespace Valera\Tests\Parser\Generic;

use Valera\Content;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result;

class Blob implements ParserInterface
{
    public function parse(Content $content, Result $result)
    {
        $source = $content->getSource();
        $resource = $source->getResource();
        $data = $content->getContent();
        $result->addBlob($resource, $data);
    }
}
