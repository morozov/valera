<?php

namespace Valera\Parser;

/**
 * Adapter of arbitrary parser interfaces
 *
 * @see \Valera\Parser\Facade
 */
interface AdapterInterface
{
    /**
     * Returns whether the given parser is supported by adapter
     *
     * @param mixed $parser
     *
     * @return boolean
     */
    public function supports($parser);

    /**
     * Wraps parser into standard interface
     *
     * @param mixed $parser
     *
     * @return callable
     */
    public function wrap($parser);
}
