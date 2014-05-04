<?php

namespace Valera\Parser;

use Valera\Content;

interface ParserInterface
{
    public function parse(Content $content, Result $result);
}
