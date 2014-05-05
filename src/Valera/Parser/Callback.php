<?php

namespace Valera\Parser;

use Valera\Content;

/**
 * Parser that uses callback function as its implementation. Used in order
 * to wrap parsers with non-standard interfaces into ParserInterface.
 *
 * @see \Valera\Parser\AdapterInterface
 */
class Callback implements ParserInterface
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
    public function parse(Content $content, Result $result)
    {
        $callback = $this->callback;
        return $callback($content, $result);
    }
}
