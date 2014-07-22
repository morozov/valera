<?php

namespace Valera\Parser;

use Valera\Content;
use Valera\Resource;

interface ParserInterface
{
    public function parse(Content $content, Result $result, Resource $resource);
}
