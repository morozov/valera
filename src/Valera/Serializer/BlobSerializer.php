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
        $serialized = array(
            'path' => $blob->getPath(),
        );

        $resource = $blob->getResource();
        if ($resource) {
            $serialized['resource'] = $this->resourceSerializer->serialize($resource);
        }

        return $serialized;
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
        if (isset($params['resource'])) {
            $resource = $this->resourceSerializer->unserialize($params['resource']);
        } else {
            $resource = null;
        }

        return new Blob($params['path'], $resource);
    }
}
