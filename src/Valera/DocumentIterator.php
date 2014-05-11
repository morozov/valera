<?php

namespace Valera;

class DocumentIterator
{
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
