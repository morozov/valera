<?php

namespace Valera\Tests\Parser\Factory;

use Valera\Content;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result;
use Valera\Resource;

class SomeParser implements ParserInterface
{
    public function parse(Content $content, Result $result, Resource $resource)
    {
    }
}
