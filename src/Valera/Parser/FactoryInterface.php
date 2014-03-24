<?php

namespace Valera\Parser;

interface FactoryInterface
{
    /**
     * Returns parser of the given type
     *
     * @param string $type Parser type
     *
     * @return \Valera\Parser\ParserInterface
     * @throws \UnexpectedValueException
     */
    public function getParser($type);
}
