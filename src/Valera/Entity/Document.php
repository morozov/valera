<?php

namespace Valera\Entity;

use Assert\Assertion;
use Valera\Blob;
use Valera\Resource;
use Valera\Value\Reference;

/**
 * Document entity
 */
class Document
{
    /**
     * Document ID
     *
     * @var string
     */
    private $id;

    /**
     * Document data
     *
     * @var array
     */
    private $data;

    /**
     * Whether document data was updated
     *
     * @var boolean
     */
    private $isDirty = false;

    /**
     * Constructor
     *
     * @param string $id   Document ID
     * @param array  $data Document data
     */
    public function __construct($id, array $data)
    {
        Assertion::string($id);

        $this->id = $id;
        $this->data = $data;
    }

    /**
     * Returns document ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns document data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns whether document data was updated
     *
     * @return boolean
     */
    public function isDirty()
    {
        return $this->isDirty;
    }

    /**
     * Updates document by applying callback to its data
     *
     * @param callable $callback Callback to apply
     */
    public function update(callable $callback)
    {
        $this->data = $callback($this->data);
        $this->isDirty = true;
    }

    /**
     * Returns embedded resources
     *
     * @return \Valera\Resource[]
     */
    public function getResources()
    {
        $resources = array();
        $this->iterate(function ($value) {
            return $value instanceof Resource;
        }, function (Resource $value) use (&$resources) {
            $resources[] = $value;
        });

        return $resources;
    }

    /**
     * Replaces embedded resource with blob
     *
     * @param \Valera\Blob $blob Replacement blob
     */
    public function replaceResource(Blob $blob)
    {
        $resource = $blob->getResource();
        $this->iterate(function ($value) use ($resource) {
            return $value instanceof Resource
                && $value->getHash() === $resource->getHash();
        }, function (Resource &$value) use ($resource, $blob) {
            $value = $blob;
            $this->isDirty = true;
        });
    }

    /**
     * Replaces references with resources
     *
     * @param string $referrer Referrer
     */
    public function replaceReference($referrer)
    {
        $this->iterate(function ($value) use ($referrer) {
            return $value instanceof Reference;
        }, function (Reference &$value) use ($referrer) {
            $value = $value->getResource($referrer);
        });
    }

    /**
     * Helper function to recursively iterate over document data leaves
     *
     * @param callable $filter   Applied to leaf value
     * @param callable $callback Called with values of filtered leaves
     */
    public function iterate(
        callable $filter,
        callable $callback
    ) {
        array_walk_recursive($this->data, function (&$value, $key) use ($filter, $callback) {
            if ($filter($value)) {
                $callback($value, array($key));
            }
        });
    }
}
