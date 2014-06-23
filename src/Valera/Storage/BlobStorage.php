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

    /**
     * Returns whether the specified resource is stored
     *
     * @param \Valera\Resource $resource
     *
     * @return boolean
     */
    public function isStored(Resource $resource);

    /**
     * Returns path corresponding to the given resource
     *
     * @param \Valera\Resource $resource
     *
     * @return string
     */
    public function getPath(Resource $resource);

    /**
     * Returns blob contents
     *
     * @param \Valera\Resource $resource
     *
     * @return string
     */
    public function retrieve(Resource $resource);
    public function delete(Resource $resource);
    public function clean();
}
