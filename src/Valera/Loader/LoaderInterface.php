<?php

namespace Valera\Loader;

use Valera\Source;
use Valera\Loader\Result\Proxy as Result;

interface LoaderInterface
{
    public function load(Source $source, Result $result);
}
