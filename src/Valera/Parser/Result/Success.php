<?php

namespace Valera\Parser\Result;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Valera\Resource;

class Success implements ResultInterface, IteratorAggregate, Countable
{
    /**
     * Parsed data
     *
     * @var mixed
     */
    private $data;

    /**
     * Additional resources to be parsed
     *
     * @var \Valera\Resource[]
     */
    private $resources = array();

    public function __construct($data)
    {
        $this->data = $data;
    }

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
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
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
