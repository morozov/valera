<?php

namespace Valera\Storage\DocumentStorage\Mongo;

class Iterator extends \IteratorIterator
{
    public function __construct(\MongoCursor $cursor)
    {
        parent::__construct($cursor);
    }

    public function key()
    {
        $current = parent::current();
        $key = $current['_id'];

        return $key;
    }

    public function current()
    {
        $current = parent::current();
        $current = $current['data'];

        return $current;
    }
}
