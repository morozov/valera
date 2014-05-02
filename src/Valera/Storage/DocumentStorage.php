<?php

namespace Valera\Storage;

interface DocumentStorage extends \Countable, \IteratorAggregate
{
    public function create($id, array $data, array $blobs);
    public function retrieve($id);

    /**
     * @param $hash
     * @return array
     */
    public function findByBlob($hash);
    public function update($id, array $data, array $blobs);
    public function delete($id);
    public function clean();
}
