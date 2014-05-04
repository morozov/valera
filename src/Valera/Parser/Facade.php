<?php

namespace Valera\Parser;

use Valera\Content;

class Facade implements ParserInterface
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function parse(Content $content, Result $result)
    {
        $type = $content->getType();
        $parser = $this->factory->getParser($type);
        if ($parser) {
            return $parser->parse($content, $result);
        }

        $result->fail(sprintf(
            'Unable to load parser of type %s',
            $type
        ));

        return null;
    }
}
