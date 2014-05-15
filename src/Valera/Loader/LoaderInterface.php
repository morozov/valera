<?php

namespace Valera\Loader;

use Valera\Resource;

interface LoaderInterface
{
    public function load(Resource $resource, Result $result);
}
