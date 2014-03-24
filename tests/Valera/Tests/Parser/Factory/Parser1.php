<?php

namespace Valera\Tests\Parser\Factory;

use Valera\Content;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result\Proxy as Result;

class Parser1 implements ParserInterface
{
    public function parse(Content $content, Result $result)
    {
    }
}
