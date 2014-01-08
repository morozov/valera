<?php

namespace Valera\Worker;

interface ParserFactoryInterface
{
    public function getParser($type);
}
