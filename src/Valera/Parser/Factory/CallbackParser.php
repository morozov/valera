<?php

namespace Valera\Parser\Factory;

use Valera\Content;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result;
use Valera\Resource;

/**
 * Parser that uses callback function as its implementation. Used in order
 * to wrap parsers with non-standard interfaces into ParserInterface.
 *
 * @see \Valera\Parser\AdapterInterface
 */
class CallbackParser implements ParserInterface
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * Constructor
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(Content $content, Result $result, Resource $resource)
    {
        $callback = $this->callback;
        return $callback($content, $result, $resource);
    }
}
