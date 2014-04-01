<?php

namespace Valera\Loader;

use Valera\Resource;
use Valera\Result\Proxy as Result;

interface LoaderInterface
{
    public function load(Resource $resource, Result $result);
}
