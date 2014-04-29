<?php

namespace Valera\Storage;

use Countable;
use Valera\Resource;

interface BlobStorage extends Countable
{
    public function create(Resource $resource, $data);
    public function retrieve(Resource $resource);
    public function delete(Resource $resource);
    public function clean();
}
