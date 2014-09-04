<?php

namespace Valera\Tests\Parser\Factory;

use Valera\Parser\ParserInterface;

class SomeParser implements ParserInterface
{
    public function process(\Traversable $tasks, callable $resolver)
    {
    }
}
