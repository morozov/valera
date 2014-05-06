<?php

namespace Valera\Serializer;

use Valera\Blob;

/**
 * Blob value object serializer
 */
class BlobSerializer implements SerializerInterface
{
    /**
     * @var ResourceSerializer
     */
    protected $resourceSerializer;

    /**
     * Constructor
     *
     * @param ResourceSerializer $resourceSerializer
     */
    public function __construct(ResourceSerializer $resourceSerializer)
    {
        $this->resourceSerializer = $resourceSerializer;
    }

    /**
     * Creates array representation of blob value object
     *
     * @param Blob $blob
     *
     * @return array
     */
    public function serialize($blob)
    {
        return array(
            'path' => $blob->getPath(),
            'resource' => $this->resourceSerializer->serialize($blob->getResource()),
        );
    }

    /**
     * Restores blob value object from array representation
     *
     * @param array $params
     *
     * @return Blob
     * @throws \InvalidArgumentException
     */
    public function unserialize(array $params)
    {
        return new Blob(
            $params['path'],
            $this->resourceSerializer->unserialize($params['resource'])
        );
    }
}
