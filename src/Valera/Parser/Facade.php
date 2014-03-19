<?php

namespace Valera\Worker;

use Valera\Content;
use Valera\Parser\FactoryInterface;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result\Proxy as Result;

class Facade implements ParserInterface
{
    /**
     * @var
     */
    protected $factory;

    /**
     * @param $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function parse(Content $content, Result $result)
    {
        $type = $content->getType();
        $parser = $this->factory->getParser($type);
        if (!$parser) {
            $result->fail(sprintf(
                'Unable to load parser of type %s',
                $type
            ));
            return;
        }

        $parser->parse($content, $result);
    }
}
