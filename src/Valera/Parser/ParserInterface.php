<?php

namespace Valera\Parser;

use Valera\Content;
use Valera\Parser\Result\Proxy as Result;

interface ParserInterface
{
    public function parse(Content $content, Result $result);
}
