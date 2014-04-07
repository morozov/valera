<?php

namespace Valera;

use Serializable;
use Valera\Serialize\Serializer;

/**
 */
class Source implements Queueable
{
    /**
     * @var \Valera\Resource
     */
    private $resource;

    /**
     * @var string
     */
    private $type;

    /**
     * @param \Valera\Resource $resource
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Resource $resource, $type)
    {
        if (!is_string($type)) {
            throw new \InvalidArgumentException(
                sprintf('Type should be a string, %s given', gettype($type))
            );
        }

        $this->resource = $resource;
        $this->type = $type;
    }

    /**
     * Returns the resource representing the source
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Returns the type of the source
     */
    public function getType()
    {
        return $this->type;
    }

    public static function fromArray(array $params)
    {
        if (!isset($params['resource'])) {
            throw new \Exception('Source resource is not set');
        }

        if (!isset($params['type'])) {
            throw new \Exception('Source type is not set');
        }

        return new self(
            Resource::fromArray($params['resource']),
            $params['type']
        );
    }

    /**
     * Returns source hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->resource->getHash();
    }

    public function accept(Serializer $serializer)
    {
        return $serializer->serializeSource($this);
    }
}
