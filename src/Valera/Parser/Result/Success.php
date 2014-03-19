<?php

namespace Valera\Parser\Result;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Valera\Resource;

class Success implements ResultInterface, IteratorAggregate, Countable
{
    protected $resources = array();

    public function addResource(
        $type,
        $url,
        $method = Resource::METHOD_GET,
        array $headers = array(),
        array $data = array()
    ) {
        $this->resources[] = new Resource($type, $url, $method, $headers, $data);
    }

    /**
     * @return \Iterator|\Valera\Resource[]
     */
    public function getResources()
    {
        return new ArrayIterator($this->resources);
    }

    /**
     * Retrieve an external iterator
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->getResources();
    }

    /**
     * Count elements of an object
     * 
     * @return int
     */
    public function count()
    {
        return count($this->resources);
    }
}
