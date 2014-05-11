<?php

namespace Valera\Storage;

use Valera\Resource;

interface DocumentStorage extends \Countable, \IteratorAggregate
{
    public function create($id, array $data, array $resources);
    public function retrieve($id);

    /**
     * @param Resource $resource
     *
     * @return \Iterator
     */
    public function findByResource(Resource $resource);
    public function update($id, array $data, array $resources);
    public function delete($id);
    public function clean();
}
