<?php

namespace Valera\Tests\Parser\Factory;

use Valera\Parser\ParserInterface;
use Valera\Queue\Resolver;

class SomeParser implements ParserInterface
{
    public function process(\Traversable $tasks, Resolver $resolver)
    {
    }
}
