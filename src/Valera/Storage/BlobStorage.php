<?php

namespace Valera\Storage;

use Countable;
use Valera\Resource;

interface BlobStorage extends Countable
{
    /**
     * Stores blob contents and returns its path
     *
     * @param \Valera\Resource $resource
     * @param string $contents
     *
     * @return string
     */
    public function create(Resource $resource, $contents);
    public function retrieve(Resource $resource);
    public function delete(Resource $resource);
    public function clean();
}
