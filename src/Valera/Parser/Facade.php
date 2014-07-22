<?php

namespace Valera\Parser;

use Valera\Content;
use Valera\Resource;

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

    public function parse(Content $content, Result $result, Resource $resource)
    {
        $type = $content->getType();
        $parser = $this->factory->getParser($type);
        if ($parser) {
            return $parser->parse($content, $result, $resource);
        }

        $result->fail(sprintf(
            'Unable to load parser of type %s',
            $type
        ));

        return null;
    }
}
