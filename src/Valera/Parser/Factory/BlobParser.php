<?php

namespace Valera\Parser\Factory;

use Valera\Content;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result;

class BlobParser implements ParserInterface
{
    public function parse(Content $content, Result $result)
    {
        $source = $content->getSource();
        $resource = $source->getResource();
        $result->addBlob($resource, $content->getContent());
    }
}
