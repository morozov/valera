<?php

namespace Valera;

use Valera\Blob;
use Valera\Resource;

class DocumentIterator
{
    /**
     * @param array $document
     *
     * @return array
     */
    public function findEmbedded(array $document)
    {
        $embedded = array();
        $this->iterate($document, function ($value) {
            return $value instanceof Resource;
        }, function (Resource $value) use (&$embedded) {
            $embedded[] = $value->getHash();
        });

        return $embedded;
    }

    public function convertEmbedded(array &$document, Resource $resource, $path)
    {
        $this->iterate($document, function ($value) use ($resource) {
            return $value instanceof Resource
            && $value->getHash() === $resource->getHash();
        }, function (Resource &$value) use ($resource, $path) {
            $value = new Blob($path, $resource);
        });
    }

    public function iterate(
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
