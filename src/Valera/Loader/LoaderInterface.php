<?php

namespace Valera\Loader;

use Valera\Source;

interface LoaderInterface
{
    public function load(Source $source, Result $result);
}
