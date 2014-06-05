<?php

namespace Valera\Serializer;

use Valera\Source\BlobSource;
use Valera\Source\DocumentSource;

/**
 * Source value object serializer
 */
class SourceSerializer implements SerializerInterface
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
     * Creates array representation of source value object
     *
     * @param \Valera\Source $source
     *
     * @return array
     */
    public function serialize($source)
    {
        $serialized = array(
            'resource' => $this->resourceSerializer->serialize($source->getResource()),
        );

        if ($source instanceof DocumentSource) {
            $serialized['type'] = $source->getType();
        }

        return $serialized;
    }

    /**
     * Restores source value object from array representation
     *
     * @param array $params
     *
     * @return \Valera\Source
     * @throws \InvalidArgumentException
     */
    public function unserialize(array $params)
    {
        $resource = $this->resourceSerializer->unserialize($params['resource']);
        if (isset($params['type'])) {
            $source = new DocumentSource($params['type'], $resource);
        } else {
            $source = new BlobSource($resource);
        }

        return $source;
    }
}
