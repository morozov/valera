<?php

namespace Valera;

use Valera\Blob\Local as LocalBlob;
use Valera\Blob\Remote as RemoteBlob;

class DocumentIterator
{
    /**
     * @param array $document
     *
     * @return array
     */
    public function findBlobs(array $document)
    {
        $blobs = array();
        $this->iterate($document, function ($value) {
            return $value instanceof RemoteBlob;
        }, function (RemoteBlob $value) use (&$blobs) {
            $blobs[] = $value->getHash();
        });

        return $blobs;
    }

    public function convertBlob(array &$document, $hash, $path)
    {
        $this->iterate($document, function ($value) use ($hash) {
            return $value instanceof RemoteBlob
            && $value->getHash() === $hash;
        }, function (RemoteBlob &$value) use ($path) {
            $value = new LocalBlob($path);
        });
    }

    protected function iterate(
        array &$document,
        callable $filter,
        callable $callback
    ) {
        array_walk_recursive($document, function (&$value) use ($filter, $callback) {
            if ($filter($value)) {
                $callback($value);
            }
        });
    }
}
